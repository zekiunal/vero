# PDF Generation Service

A PHP-based web service that authenticates with an API, retrieves task data, and generates downloadable PDF documents from the data. Built with clean architecture principles and a distributed system approach.

## Features

- Authentication with the BauBuddy API
- Secure token caching using Redis
- Task data retrieval from the API
- Dynamic HTML generation from task data
- PDF creation using wkhtmltopdf
- Background worker for notifications
- RESTful API with Swagger documentation
- Health check endpoint for monitoring
- Comprehensive test coverage with PHPUnit and Behat

## Technology Stack

- PHP 8.4
- Slim Framework 4
- Docker & Docker Compose
- Redis for caching and messaging
- Nginx as web server
- wkhtmltopdf for PDF generation

## Project Structure

The project follows clean architecture principles with the following layers:

- **Domain**: Core business logic, entities, and interfaces
- **Application**: Use cases, controllers, and middleware
- **Infrastructure**: External services, repositories, and frameworks

## Getting Started

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/pdf-service.git
   cd pdf-service
   ```

2. Create a `.env` file with the following content:
   ```
   API_BASE_URL=https://api.baubuddy.de
   REDIS_HOST=redis
   REDIS_PORT=6379
   DISPLAY_ERROR_DETAILS=true
   LOG_ERRORS=true
   LOG_ERROR_DETAILS=true
   API_VERSION=1.0.0
   ```

3. Build and start the Docker containers:
   ```bash
   docker compose up -d --build
   ```

4. Install dependencies:
   ```bash
   docker compose exec app composer install
   ```

5. Run tests:
   ```bash
   docker compose exec app composer test
   docker compose exec app composer behat
   ```

## Usage

### Generate a PDF

To generate a PDF, make a GET request to the `/service` endpoint with `username` and `password` parameters:

```
GET http://localhost:8080/service?username=365&password=1
```

The service will respond with a PDF file for download.

### Health Check

To check the health of the service:

```
GET http://localhost:8080/health
```

### API Documentation

The API documentation is available at:

```
GET http://localhost:8080/api/docs
```

## Architecture

### Components

- **Entities**: Domain objects like `Task`
- **Repositories**: Data access interfaces and implementations
- **Services**: Business logic components for authentication, PDF generation, etc.
- **Use Cases**: Application-specific business rules
- **Controllers**: HTTP request handlers
- **Middleware**: Request/response processors
- **Workers**: Background processes for asynchronous operations

### Workflow

1. A request comes in with username and password
2. The service authenticates with the BauBuddy API
3. The service fetches task data from the API
4. Tasks are transformed into a formatted HTML document
5. The HTML is converted to PDF using wkhtmltopdf
6. The PDF is sent as a downloadable response
7. Notifications are published to Redis for the worker to process


## Worker

The service includes a background worker that processes notifications published to Redis. The worker subscribes to the `notifications` channel and processes messages in real-time.

### Starting the Worker

The worker is automatically started as part of the Docker Compose setup. If you need to restart it manually:

```bash
docker compose restart worker
```

## Deployment

The service is containerized using Docker, making it easy to deploy in any environment that supports Docker containers.

### Production Considerations

For production deployments, consider:

1. Setting up a reverse proxy with SSL termination
2. Configuring proper logging with log rotation
3. Using a Redis cluster for improved reliability
4. Setting appropriate resource limits for containers
5. Implementing monitoring and alerting

## API Documentation

The service provides a Swagger UI interface for API exploration. The documentation includes:

- Endpoints description
- Request parameters
- Response formats
- Response codes
- Example requests

## Swagger

```
GET http://localhost:8080/api/docs
```

### Swagger JSON

The Swagger JSON specification is available at:

```
GET http://localhost:8080/api/docs/json
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.