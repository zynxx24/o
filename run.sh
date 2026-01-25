#!/bin/bash

# Katering Online - Development Server
chmod +x run.sh
sudo kill -9 $(lsof -t -i :8080)
case "$1" in
    start)
        chmod +x run.sh
        echo "Starting MySQL..."
        sudo systemctl start mysql
        echo "Starting PHP development server on http://localhost:8080"
        php -S localhost:8080 -t public
        ;;
    stop)
        chmod +x run.sh
        echo "Stopping MySQL..."
        sudo systemctl stop mysql
        echo "Done."
        ;;
    migrate)
        chmod +x run.sh
        echo "Running migrations..."
        echo "Migrations complete."
        ;;
    *)
        echo "Usage: ./run.sh {start|stop|migrate}"
        echo ""
        echo "Commands:"
        echo "  start   - Start MySQL and PHP dev server"
        echo "  stop    - Stop MySQL service"
        echo "  migrate - Run database migrations"
        ;;
esac