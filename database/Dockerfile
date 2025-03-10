# Usa una imagen base de MySQL
FROM mysql:8.0

# Establece variables de entorno
ENV MYSQL_ROOT_PASSWORD=p4ssw0rd.
ENV MYSQL_DATABASE=sistema_usuarios
ENV MySQL_USER=ctf_user
ENV MySQL_PASSWORD=p4ssw0rd.

# Copia el script SQL para crear la base de datos
COPY esquema.sql /docker-entrypoint-initdb.d/

# Exponer el puerto 3306
#EXPOSE 3306
