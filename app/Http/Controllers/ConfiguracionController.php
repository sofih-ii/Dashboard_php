<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class ConfiguracionController extends Controller
{
    public function updateNotifications(Request $request)
    {
        $settings = [
            'nueva_venta'      => $request->boolean('nueva_venta'),
            'nuevo_cliente'    => $request->boolean('nuevo_cliente'),
            'factura_vencida'  => $request->boolean('factura_vencida'),
            'mensaje_nuevo'    => $request->boolean('mensaje_nuevo'),
            'alerta_seguridad' => $request->boolean('alerta_seguridad'),
            'reporte_semanal'  => $request->boolean('reporte_semanal'),
        ];

        Auth::user()->update(['notification_settings' => $settings]);

        return back()
            ->with('success_notif', 'Preferencias de notificaciones guardadas.')
            ->with('tab', 'notificaciones');
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'theme'    => 'nullable|in:light,dark',
            'per_page' => 'nullable|integer|in:10,25,50',
        ]);

        Auth::user()->update([
            'theme'    => $request->theme    ?? 'light',
            'per_page' => $request->per_page ?? 25,
        ]);

        return back()
            ->with('success_system', 'Configuración del sistema guardada.')
            ->with('tab', 'sistema');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return back()
            ->with('success_system', 'Caché limpiada correctamente.')
            ->with('tab', 'sistema');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password_confirm' => 'required|string',
        ], [
            'password_confirm.required' => 'Debes ingresar tu contraseña para confirmar.',
        ]);

        if (!Hash::check($request->password_confirm, Auth::user()->password)) {
            return back()
                ->withErrors(['password_confirm' => 'Contraseña incorrecta.'])
                ->with('tab', 'sistema');
        }

        $user = Auth::user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();

        return redirect('/')->with('deleted', 'Tu cuenta ha sido eliminada correctamente.');
    }
}
