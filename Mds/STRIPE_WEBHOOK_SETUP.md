# Configuración de Webhooks de Stripe

## ✅ Configuración Completada

### 🔧 Webhook Configurado
- **URL del webhook (desarrollo):** `http://localhost:8000/stripe/webhook`
- **URL del webhook (túnel opcional):** `https://<subdominio>.ngrok-free.app/stripe/webhook`
- **ID del webhook:** configurar desde el Dashboard de Stripe
- **Secret del webhook:** mantener en `.env` (no documentar en texto plano)

### 📝 Variables de Entorno
Agregar al archivo `.env` (usar valores propios de tu entorno):
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 🎯 Eventos Configurados
- `payment_intent.succeeded` - Pago exitoso
- `payment_intent.payment_failed` - Pago fallido
- `payment_intent.canceled` - Pago cancelado
- `charge.succeeded` - Cargo exitoso
- `charge.failed` - Cargo fallido

### 🔄 Estados de Pedido
- **Estado 1:** Pendiente
- **Estado 2:** Confirmado (pago exitoso)
- **Estado 3:** Cancelado (pago fallido/cancelado)

## 🧪 Comandos de Prueba

### Probar webhook localmente:
```bash
php artisan stripe:test-webhook-local --event=payment_intent.succeeded --pedido=1
php artisan stripe:test-webhook-local --event=payment_intent.payment_failed --pedido=2
php artisan stripe:test-webhook-local --event=payment_intent.canceled --pedido=3
```

### Verificar configuración:
```bash
php artisan stripe:check-webhooks
php artisan check:pedidos
php artisan check:estados-pedido
```

## 🔗 URLs Importantes

### Webhook de Stripe
- **URL (local):** `http://localhost:8000/stripe/webhook`
- **URL (túnel):** `https://<subdominio>.ngrok-free.app/stripe/webhook`
- **Método:** POST
- **Autenticación:** Firma de Stripe

### Rutas de la Aplicación
- **Formulario de pago:** `/stripe/payment-form/{pedidoId}`
- **Crear Payment Intent:** `/stripe/create-payment-intent`
- **Confirmar pago:** `/stripe/confirm-payment`

## 📊 Flujo de Webhooks

1. **Pago Exitoso:**
   - Evento: `payment_intent.succeeded`
   - Acción: Cambiar estado a 2 (Confirmado)
   - Crear/actualizar registro de pago

2. **Pago Fallido:**
   - Evento: `payment_intent.payment_failed`
   - Acción: Cambiar estado a 3 (Cancelado)
   - Actualizar registro de pago

3. **Pago Cancelado:**
   - Evento: `payment_intent.canceled`
   - Acción: Cambiar estado a 3 (Cancelado)
   - Actualizar registro de pago

## 🔒 Seguridad

- **Verificación de firma:** Habilitada para producción
- **Modo de prueba:** Flexibilidad en verificación de firma
- **Logs:** Todos los eventos se registran en logs

## 🚀 Próximos Pasos

1. **Configurar webhook en producción** cuando esté listo
2. **Agregar notificaciones por email** para eventos importantes
3. **Implementar retry logic** para webhooks fallidos
4. **Agregar dashboard** para monitorear webhooks
