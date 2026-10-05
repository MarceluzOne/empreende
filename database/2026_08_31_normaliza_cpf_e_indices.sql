-- ============================================================
-- Empreende Vitoria - normalizacao de CPF + indices
-- Rodar no phpMyAdmin (producao nao tem SSH/artisan).
-- FACA BACKUP ANTES. Validado sobre o dump prefeituradavitoria8.sql.
-- ============================================================
START TRANSACTION;

-- ------------------------------------------------------------
-- 1) Normaliza documentos para SO DIGITOS
--    job_seekers.cpf e empresas.cnpj gravavam com mascara;
--    todas as outras tabelas ja gravam so digitos.
-- ------------------------------------------------------------
UPDATE job_seekers SET cpf  = REGEXP_REPLACE(cpf,  '[^0-9]', '') WHERE cpf  REGEXP '[^0-9]';
UPDATE empresas    SET cnpj = REGEXP_REPLACE(cnpj, '[^0-9]', '') WHERE cnpj REGEXP '[^0-9]';

-- ------------------------------------------------------------
-- 2) Desduplica curriculos pelo CPF
--    Mantem a linha com MAIS candidaturas (desempate: mais antiga),
--    remapeia as candidaturas das demais e so entao apaga.
-- ------------------------------------------------------------
CREATE TEMPORARY TABLE js_keep AS
SELECT cpf, id AS keep_id FROM (
  SELECT j.cpf, j.id,
         ROW_NUMBER() OVER (
           PARTITION BY j.cpf
           ORDER BY (SELECT COUNT(*) FROM job_applications a WHERE a.job_seeker_id = j.id) DESC,
                    j.created_at ASC
         ) rn
  FROM job_seekers j
  WHERE j.cpf IS NOT NULL AND j.cpf <> ''
) x WHERE rn = 1;

UPDATE job_applications a
  JOIN job_seekers j ON j.id = a.job_seeker_id
  JOIN js_keep k     ON k.cpf = j.cpf
SET a.job_seeker_id = k.keep_id
WHERE a.job_seeker_id <> k.keep_id;

DELETE j FROM job_seekers j
  JOIN js_keep k ON k.cpf = j.cpf
WHERE j.id <> k.keep_id;

DROP TEMPORARY TABLE js_keep;

-- ------------------------------------------------------------
-- 3) Padroniza o tipo das colunas de documento
--    CPF = 11 digitos, CNPJ = 14. bookings.cpf era varchar(255).
-- ------------------------------------------------------------
ALTER TABLE job_seekers        MODIFY cpf           VARCHAR(11) NULL;
ALTER TABLE speakers           MODIFY cpf           VARCHAR(11) NULL;
ALTER TABLE event_participants MODIFY cpf           VARCHAR(11) NULL;
ALTER TABLE attendances        MODIFY customer_cpf  VARCHAR(11) NULL;
ALTER TABLE attendances        MODIFY customer_cnpj VARCHAR(14) NULL;
ALTER TABLE bookings           MODIFY cpf           VARCHAR(11) NULL;
ALTER TABLE empresas           MODIFY cnpj          VARCHAR(14) NOT NULL;

-- ------------------------------------------------------------
-- 4) Indices (todo lookup por CPF hoje e full table scan)
-- ------------------------------------------------------------
CREATE INDEX job_seekers_cpf_index                ON job_seekers        (cpf);
CREATE INDEX job_seekers_email_index              ON job_seekers        (email);
CREATE INDEX job_seekers_area_status_index        ON job_seekers        (interest_area, status);
CREATE INDEX event_participants_cpf_index         ON event_participants (cpf);
CREATE INDEX event_participants_email_index       ON event_participants (email);
CREATE INDEX attendances_customer_cpf_index       ON attendances        (customer_cpf);
CREATE INDEX attendances_status_scheduled_index   ON attendances        (status, scheduled_at);
CREATE INDEX service_providers_status_type_index  ON service_providers  (status, provider_type);
CREATE INDEX service_providers_email_index        ON service_providers  (email);
CREATE INDEX job_vacancies_status_area_index      ON job_vacancies      (status, interest_area);
CREATE INDEX events_visibility_status_date_index  ON events             (visibility, status, date);

COMMIT;
