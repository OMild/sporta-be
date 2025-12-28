# Sporta

Sporta application with Docker, MySQL, and Redis.

## Port Configuration

| Service | Internal Port | External Port |
|---------|--------------|---------------|
| App     | 80           | **8088**      |
| MySQL   | 3306         | **3307**      |
| Redis   | 6379         | **6380**      |

## Quick Start

```bash
# Build and run
make build
make up

# Or directly
docker compose up -d --build
```

Access the application at: http://localhost:8088

## Commands

```bash
make help       # Show all commands
make up         # Start containers
make down       # Stop containers
make logs       # View logs
make shell      # Access app container shell
make artisan cmd="migrate"     # Run artisan command
make composer cmd="require X"  # Run composer command
make mysql      # Access MySQL CLI
make redis      # Access Redis CLI
make fresh      # Fresh install (reset everything)
```

## Database Connection

For connection from host (TablePlus, DBeaver, etc):
- Host: `localhost`
- Port: `3307`
- Database: `sporta`
- Username: `sporta`
- Password: `secret`

## Redis Connection

For connection from host:
- Host: `localhost`
- Port: `6380`

## Structure

```
.
├── docker/
│   ├── mysql/my.cnf
│   ├── nginx/
│   ├── php/php.ini
│   └── supervisor/
├── src/                 # Application source
├── docker-compose.yml
├── Dockerfile
└── Makefile
```
