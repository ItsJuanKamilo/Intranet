CONFIGURAR PERMISOS
```bash

sudo usermod -aG www-data artilec && \
sudo chown -R artilec:www-data /opt/intranet && \
sudo find /opt/intranet -type d -exec chmod 775 {} \; && \
sudo find /opt/intranet -type f -exec chmod 664 {} \; && \
sudo chmod -R 775 /opt/intranet/storage && \
sudo chmod -R 775 /opt/intranet/bootstrap/cache && \
sudo chmod -R 775 /opt/intranet/storage/logs && \
sudo setfacl -R -m u:artilec:rwx /opt/intranet && \
sudo setfacl -R -m g:www-data:rwx /opt/intranet && \
sudo systemctl restart apache2

```

INICIAR PROYECTO


php artisan migrate --path="database/migrations/2025_03_25_180852_create_catalogs_tables.php"
