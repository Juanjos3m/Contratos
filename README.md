# API de Contratos - Arquitectura en Capas

Este proyecto implementa una API REST para la gestión de contratos con cálculo de cuotas de pago, utilizando una arquitectura en capas limpia y organizada.

## 📋 Descripción

La aplicación permite:
- Crear contratos con número, fecha, valor total y método de pago
- Proyectar las cuotas de pago de un contrato según el número de meses y el servicio de pago seleccionado
- Calcular automáticamente intereses y tarifas según el método de pago (PayPal o PayOnline)

## 🏗️ Arquitectura

El proyecto está organizado en capas siguiendo los principios de Clean Architecture y DDD (Domain-Driven Design):

```
┌─────────────────────────────────────────────────────────┐
│                    Controller Layer                      │
│  ┌──────────────────────────────────────────────────┐   │
│  │  ContractController (REST Endpoints)             │   │
│  └──────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────┘
                        │
┌───────────────────────▼─────────────────────────────────┐
│                  Application Layer                       │
│  ┌──────────────────────────────────────────────────┐   │
│  │  CreateContractService                            │   │
│  │  InstallmentProjectionService                     │   │
│  │  PaymentServiceFactory                            │   │
│  └──────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────┐   │
│  │  DTOs (Request/Response)                          │   │
│  └──────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────┘
                        │
┌───────────────────────▼─────────────────────────────────┐
│                    Domain Layer                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │  PaymentMethod (Enum)                            │   │
│  │  PaymentService (Interface)                      │   │
│  │  ContractRepositoryInterface                     │   │
│  └──────────────────────────────────────────────────┘   │
└───────────────────────┬─────────────────────────────────┘
                        │
┌───────────────────────▼─────────────────────────────────┐
│                Infrastructure Layer                      │
│  ┌──────────────────────────────────────────────────┐   │
│  │  Contract (Doctrine Entity)                      │   │
│  │  ContractRepository (Doctrine)                   │   │
│  │  PayPalPaymentService                            │   │
│  │  PayOnlinePaymentService                         │   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

### Principios de la Arquitectura:

1. **Separación de Responsabilidades**: Cada capa tiene una responsabilidad clara
2. **Inversión de Dependencias**: Las capas superiores dependen de interfaces definidas en Domain
3. **Independencia de Frameworks**: La lógica de negocio no depende de Doctrine ni Symfony
4. **Testabilidad**: Cada componente puede ser testeado de forma independiente

### **Domain Layer** (`src/Domain/`)
Contiene la lógica de negocio pura, sin dependencias externas:
- `Enum/PaymentMethod.php` - Enum para métodos de pago
- `Payment/PaymentService.php` - Interface para servicios de pago
- `Repository/ContractRepositoryInterface.php` - Interface del repositorio

### **Application Layer** (`src/Application/`)
Contiene los casos de uso y orquestación:
- `Contract/CreateContractService.php` - Servicio para crear contratos
- `Installment/InstallmentProjectionService.php` - Servicio para proyectar cuotas
- `Payment/PaymentServiceFactory.php` - Factory para obtener servicios de pago
- `DTO/` - Objetos de transferencia de datos (Request/Response)

### **Infrastructure Layer** (`src/Infrastructure/`)
Contiene las implementaciones técnicas:
- `Entity/Contract.php` - Entidad Doctrine para persistencia
- `Repository/ContractRepository.php` - Implementación del repositorio con Doctrine
- `Payment/PayPalPaymentService.php` - Implementación del servicio PayPal
- `Payment/PayOnlinePaymentService.php` - Implementación del servicio PayOnline

### **Controller Layer** (`src/Controller/`)
Contiene los controladores REST:
- `ContractController.php` - Endpoints REST para contratos

## 🔧 Servicios de Pago

### PayPal
- Interés mensual: 1% del saldo pendiente
- Tarifa de pago: 2% del subtotal

### PayOnline
- Interés mensual: 2% del saldo pendiente
- Tarifa de pago: 1% del subtotal

## 📡 Endpoints REST

### POST `/contracts`
Crea un nuevo contrato.

**Request Body:**
```json
{
  "contractNumber": "CT-001",
  "contractDate": "2024-01-15",
  "totalAmount": "10000.00",
  "paymentMethod": "PAYPAL"
}
```

**Response (201):**
```json
{
  "id": 1,
  "contractNumber": "CT-001"
}
```

### GET `/contracts/{id}/installments/projection`
Obtiene la proyección de cuotas para un contrato.

**Query Parameters:**
- `months` (opcional): Número de meses (default: 1)
- `method` (opcional): Método de pago a usar (PAYPAL o PAYONLINE, default: método del contrato)

**Response (200):**
```json
{
  "contractId": 1,
  "months": 3,
  "method": "PAYPAL",
  "installments": [
    {
      "installment": 1,
      "dueDate": "2024-02-15",
      "base": 3333.33,
      "interest": 100.00,
      "fee": 68.67,
      "total": 3502.00,
      "paymentMethod": "PAYPAL"
    },
    ...
  ]
}
```

## 🚀 Instalación

1. Instalar dependencias:
```bash
composer install
```

2. Configurar base de datos en `.env`:
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/contratos_db?serverVersion=8.0"
```

3. Ejecutar migraciones:
```bash
php bin/console doctrine:migrations:migrate
```

4. Iniciar servidor:
```bash
symfony server:start
```

## 📝 Estructura de Carpetas

```
src/
├── Application/          # Casos de uso y DTOs
│   ├── Contract/
│   ├── DTO/
│   ├── Installment/
│   └── Payment/
├── Controller/          # Controladores REST
├── Domain/              # Lógica de negocio pura
│   ├── Enum/
│   ├── Payment/
│   └── Repository/
└── Infrastructure/      # Implementaciones técnicas
    ├── Entity/
    ├── Payment/
    └── Repository/
```

## 🧪 Testing

```bash
php bin/phpunit
```

## 📄 Licencia

Proprietary
