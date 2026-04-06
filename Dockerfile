# Usamos a imagem oficial do PHP 8.3 com FPM
FROM php:8.3-fpm

# Instala as dependências do sistema e bibliotecas para o PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nginx

# Limpa o cache do apt para reduzir o tamanho da imagem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP necessárias (foco no pdo_pgsql para o Supabase)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Instala o Composer vindo da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho dentro do container
WORKDIR /var/www

# Copia os arquivos do seu projeto para o container
COPY . .

# Instala as dependências do Laravel (sem as de desenvolvimento)
RUN composer install --no-dev --optimize-autoloader

# Ajusta as permissões das pastas que o Laravel precisa escrever
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Porta que o Render vai mapear
EXPOSE 80

# Comando para subir o servidor embutido do PHP (ideal para o plano free do Render)
CMD php artisan serve --host=0.0.0.0 --port=80