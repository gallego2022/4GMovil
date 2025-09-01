<?php

namespace App\Helpers;

class PaymentHelper
{
    /**
     * Obtener el nombre del método de pago de manera segura
     */
    public static function getMetodoPagoNombre($pedido)
    {
        if (!$pedido->pago || !$pedido->pago->metodoPago) {
            return 'No especificado';
        }
        
        return $pedido->pago->metodoPago->nombre;
    }

    /**
     * Alias para getMetodoPagoNombre - obtener el nombre del método de pago
     */
    public static function getPaymentMethodName($pedido)
    {
        return self::getMetodoPagoNombre($pedido);
    }

    /**
     * Obtener el estado del pago de manera segura
     */
    public static function getPaymentStatus($pedido)
    {
        if (!$pedido->pago) {
            return 'pendiente';
        }

        return $pedido->pago->estado ?? 'pendiente';
    }

    /**
     * Obtener la fecha del pago de manera segura
     */
    public static function getPaymentDate($pedido)
    {
        if (!$pedido->pago || !$pedido->pago->fecha_pago) {
            return $pedido->fecha_pedido;
        }

        return $pedido->pago->fecha_pago;
    }
}
