<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PayPalController extends Controller
{
    private $client;

    public function __construct()
    {
        $environment = new SandboxEnvironment(
            config('services.paypal.sandbox_client_id'),
            config('services.paypal.sandbox_secret')
        );
        $this->client = new PayPalHttpClient($environment);
    }

    public function crearOrdenPago(Request $request)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "MXN",
                    "value" => "100.00",
                    "breakdown" => [
                        "item_total" => [
                            "currency_code" => "MXN",
                            "value" => "100.00"
                        ]
                    ]
                ],
                "items" => [
                    [
                        "name" => "Cita en Barbería Moshan",
                        "description" => "Reserva de cita para corte de cabello",
                        "quantity" => "1",
                        "unit_amount" => [
                            "currency_code" => "MXN",
                            "value" => "100.00"
                        ]
                    ]
                ]
            ]],
            "application_context" => [
                "brand_name" => "Barbería Moshan",
                "return_url" => url('/pago-exitoso'),
                "cancel_url" => url('/pago-cancelado')
            ]
        ];

        try {
            $response = $this->client->execute($request);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verificarPago(Request $request)
    {
        // Aquí validarías el pago y guardarías en tu base de datos
        return response()->json(['success' => true]);
    }
}