#!/bin/bash

# Script para verificar autenticación JWT
# Uso: ./verificar-jwt.sh [email] [password]

BASE_URL="${BASE_URL:-http://localhost}"
EMAIL="${1:-admin@example.com}"
PASSWORD="${2:-password}"

echo "=========================================="
echo "Verificación de Autenticación JWT"
echo "=========================================="
echo "URL Base: $BASE_URL"
echo "Email: $EMAIL"
echo ""

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir resultados
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

# 1. Verificar que el servidor esté corriendo
echo "1. Verificando que el servidor esté corriendo..."
if curl -s -o /dev/null -w "%{http_code}" "$BASE_URL" | grep -q "200\|301\|302"; then
    print_success "Servidor está corriendo"
else
    print_error "Servidor no está respondiendo"
    exit 1
fi

# 2. Probar login API
echo ""
echo "2. Probando login API..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/api/jwt/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"correo_electronico\": \"$EMAIL\",
    \"contrasena\": \"$PASSWORD\"
  }")

if echo "$LOGIN_RESPONSE" | grep -q '"success":true'; then
    print_success "Login API exitoso"
    TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
    if [ -z "$TOKEN" ]; then
        print_error "No se pudo extraer el token de la respuesta"
        echo "Respuesta: $LOGIN_RESPONSE"
        exit 1
    fi
    print_info "Token obtenido: ${TOKEN:0:50}..."
else
    print_error "Login API falló"
    echo "Respuesta: $LOGIN_RESPONSE"
    exit 1
fi

# 3. Validar token
echo ""
echo "3. Validando token JWT..."
VALIDATE_RESPONSE=$(curl -s -X GET "$BASE_URL/api/jwt/validate" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN")

if echo "$VALIDATE_RESPONSE" | grep -q '"valid":true'; then
    print_success "Token JWT válido"
    echo "$VALIDATE_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$VALIDATE_RESPONSE"
else
    print_error "Token JWT inválido"
    echo "Respuesta: $VALIDATE_RESPONSE"
    exit 1
fi

# 4. Probar ruta protegida con token
echo ""
echo "4. Probando ruta protegida (admin) con token..."
PROTECTED_RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$BASE_URL/admin" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -b "jwt_token=$TOKEN")

HTTP_CODE=$(echo "$PROTECTED_RESPONSE" | tail -n1)
BODY=$(echo "$PROTECTED_RESPONSE" | sed '$d')

if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    print_success "Ruta protegida accesible (HTTP $HTTP_CODE)"
else
    print_error "Ruta protegida no accesible (HTTP $HTTP_CODE)"
    echo "Respuesta: $BODY"
fi

# 5. Probar ruta protegida SIN token
echo ""
echo "5. Probando ruta protegida SIN token (debe fallar)..."
NO_TOKEN_RESPONSE=$(curl -s -w "\n%{http_code}" -X GET "$BASE_URL/admin" \
  -H "Accept: application/json")

HTTP_CODE=$(echo "$NO_TOKEN_RESPONSE" | tail -n1)
BODY=$(echo "$NO_TOKEN_RESPONSE" | sed '$d')

if [ "$HTTP_CODE" = "401" ] || [ "$HTTP_CODE" = "302" ]; then
    print_success "Ruta protegida correctamente rechazada sin token (HTTP $HTTP_CODE)"
else
    print_error "Ruta protegida debería rechazar sin token (HTTP $HTTP_CODE)"
fi

# 6. Probar refresh token
echo ""
echo "6. Probando refresh token..."
REFRESH_RESPONSE=$(curl -s -X POST "$BASE_URL/api/jwt/refresh" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{}")

if echo "$REFRESH_RESPONSE" | grep -q '"success":true'; then
    print_success "Refresh token exitoso"
    NEW_TOKEN=$(echo "$REFRESH_RESPONSE" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
    if [ ! -z "$NEW_TOKEN" ]; then
        print_info "Nuevo token obtenido: ${NEW_TOKEN:0:50}..."
    fi
else
    print_error "Refresh token falló"
    echo "Respuesta: $REFRESH_RESPONSE"
fi

echo ""
echo "=========================================="
echo "Verificación completada"
echo "=========================================="

