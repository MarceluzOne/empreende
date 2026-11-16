-- ============================================================
-- Empreende Vitoria - telefone na conta do portal
-- Rodar no phpMyAdmin (producao nao tem SSH/artisan).
--
-- CONFERIR ANTES (se devolver uma linha, nao rode nada):
--   SHOW COLUMNS FROM users LIKE 'phone';
--
-- FACA BACKUP. ALTER TABLE faz commit implicito no MySQL: o
-- START TRANSACTION abaixo segue o padrao dos outros scripts,
-- mas NAO ha rollback para DDL -- o caminho de volta e o dump.
--
-- POR QUE: as outras origens (curriculo, atendimento, evento,
-- prestador) ja guardam um telefone, mas nenhuma cobre quem so
-- tem conta no portal -- justamente quem se cadastrou sozinho
-- pelo site. A tela de edicao do cidadao grava aqui.
--
-- So digitos, sem mascara: e assim que CitizenService::phonesFor()
-- compara os numeros das varias origens.
--
-- Equivale a migration 2026_08_31_000001_add_phone_to_users_table,
-- que serve ao ambiente local.
-- ============================================================
START TRANSACTION;

ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER cpf;

COMMIT;

-- Conferencia depois:
--   SHOW COLUMNS FROM users LIKE 'phone';
