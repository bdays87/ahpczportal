<?php

use App\Http\Controllers\Api\SageConnectorController;
use App\Http\Middleware\VerifySageConnectorApiKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sage Connector API Routes
|--------------------------------------------------------------------------
|
| These routes implement the Portal API Contract expected by the SageConnector
| middleware service. All routes require X-Api-Key authentication.
|
| See: SAGE_PASTEL_INTEGRATION_GUIDE.md for complete documentation
|
*/

Route::prefix('api/connector')->middleware(VerifySageConnectorApiKey::class)->group(function () {
    
    // PULL endpoints - Connector fetches pending records
    Route::get('/customers/pending', [SageConnectorController::class, 'getPendingCustomers'])
         ->name('connector.customers.pending');
    
    Route::get('/orders/pending', [SageConnectorController::class, 'getPendingOrders'])
         ->name('connector.orders.pending');
    
    // ACK endpoint - Connector acknowledges sync results
    Route::post('/ack', [SageConnectorController::class, 'receiveAck'])
         ->name('connector.ack');
    
    // PUSH endpoints - Connector pushes data from Sage
    Route::post('/invoices', [SageConnectorController::class, 'receiveInvoice'])
         ->name('connector.invoices.push');
    
    Route::post('/statements', [SageConnectorController::class, 'receiveStatement'])
         ->name('connector.statements.push');
});
