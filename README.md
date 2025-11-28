## Requirements

- Docker & Docker Compose
- Make

## Quick Start

```bash
# Build 
make build

# Run containers
make up
  
# Run all tests
make test

# Stop containers
make down

```

## Available Make Commands

| Command         | Description               |
|-----------------|---------------------------|
| `make build`    | Build Docker image        |
| `make up`       | Start containers          |
| `make down`     | Stop containers           |
| `make shell`    | Open shell in container   |
| `make test`     | Run all tests             |
| `make install`  | Install dependencies      |
| `make update`   | Update dependencies       |
| `make autoload` | Regenerate autoload files |
