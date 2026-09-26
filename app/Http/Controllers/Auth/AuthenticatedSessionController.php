<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    const MAX_ATTEMPTS = 5; // Intentos permitidos
    const LOCKOUT_MINUTES = 5; // Tiempo de bloqueo

    public function create(): View
    {
        $isBlocked = $this->isIpBlocked(request()->ip());
        $remainingAttempts = $this->getRemainingAttempts(request()->ip());

        return view('auth.login', [
            'isBlocked' => $isBlocked,
            //'remainingAttempts' => $remainingAttempts,
            'blockTime' => $this->getBlockTime(request()->ip())
        ]);
    }

    public function store(Request $request)
    {
        $ip = $request->ip();
        
        // Verificar y limpiar bloqueo si ya expiró
        if ($this->isIpBlocked($ip)) {
            $blockTime = $this->getBlockTime($ip);
            if ($blockTime <= 0) {
                $this->clearLoginAttempts($ip);
            } else {
                return back()->withErrors([
                    'nombre_usuario' => 'Demasiados intentos. Intente nuevamente en '.$blockTime.' minutos.',
                ]);
            }
        }

        $request->validate([
            'nombre_usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cambia esta línea (agrega el remember)
        if (!auth()->attempt([
            'nombre_usuario' => $request->nombre_usuario,
            'password' => $request->password
        ], $request->boolean('remember'))) {  // ← Aquí se usa el checkbox
            $this->incrementLoginAttempts($ip);
            $remainingAttempts = $this->getRemainingAttempts($ip);

            if ($remainingAttempts <= 0) {
                $this->blockIp($ip);
                return back()->withErrors([
                    'nombre_usuario' => 'Demasiados intentos. Intente nuevamente en '.self::LOCKOUT_MINUTES.' minutos.',
                ]);
            }

            return back()->withErrors([
                'nombre_usuario' => 'Usuario y/o contraseña incorrectos. Intentos restantes: '.$remainingAttempts,
            ]);
        }

        $user = Auth::user();

        if ($user->rol === 'Cliente') {
            Auth::logout();
            return redirect('/login')->withErrors(['nombre_usuario'=> 'No tienes permiso para acceder.']);
        }

        $barberoExiste = \App\Models\Barbero::where('usuario_id', $user->id)->exists();

        if ($user->rol === "Barbero" && !$barberoExiste) {  
            Auth::logout();
            return redirect('/login')->withErrors(['nombre_usuario' => 'Cuenta no registrada como barbero.']);
        }

        $this->clearLoginAttempts($ip);
        $request->session()->regenerate();
        return redirect()->intended(route('inicio'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Crear respuesta con headers anti-caché
        $response = redirect('/');

        // Headers para evitar caché en todos los navegadores
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");

        // Forzar limpieza del caché del navegador
        $response->headers->set('Clear-Site-Data', '"cache", "storage"');

        return $response;
    }

    // Métodos de bloqueo (compatibles con hosting)
    private function incrementLoginAttempts($ip): void
    {
        Cache::put(
            'login_attempts_'.$ip, 
            Cache::get('login_attempts_'.$ip, 0) + 1, 
            now()->addMinutes(self::LOCKOUT_MINUTES * 2)
        );
    }

    private function getRemainingAttempts($ip): int
    {
        return max(0, self::MAX_ATTEMPTS - Cache::get('login_attempts_'.$ip, 0));
    }

    private function isIpBlocked($ip): bool
    {
        // Si el bloqueo ya expiró, limpiarlo
        if (Cache::has('login_block_'.$ip) && !Cache::get('login_block_'.$ip)) {
            $this->clearLoginAttempts($ip);
            return false;
        }
        return Cache::has('login_block_'.$ip);
    }

    private function blockIp($ip): void
    {
        if (!Cache::has('login_block_'.$ip)) {
            // Establecer el tiempo de bloqueo exacto
            $blockUntil = now()->addMinutes(self::LOCKOUT_MINUTES);
            Cache::put('login_block_'.$ip, true, $blockUntil);
            
            // Guardar el tiempo de finalización del bloqueo en lugar del inicio
            Cache::put('login_block_'.$ip.'_time', $blockUntil, $blockUntil);
            
            // Reiniciar intentos pero con expiración al mismo tiempo que el bloqueo
            Cache::put('login_attempts_'.$ip, 0, $blockUntil);
        }
    }

    private function clearLoginAttempts($ip): void
    {
        // Eliminar todas las claves relacionadas con el bloqueo
        Cache::forget('login_attempts_'.$ip);
        Cache::forget('login_block_'.$ip);
        Cache::forget('login_block_'.$ip.'_time');
        
        // Asegurar que las claves se eliminen completamente
        if (Cache::has('login_block_'.$ip)) {
            Cache::forget('login_block_'.$ip);
        }
    }

    private function getBlockTime($ip): int
    {
        if (Cache::has('login_block_'.$ip.'_time')) {
            $blockUntil = Cache::get('login_block_'.$ip.'_time');
            $remainingMinutes = now()->diffInMinutes($blockUntil, false);
            return max(0, $remainingMinutes); // Devuelve 0 si el tiempo ya pasó
        }
        return 0;
    }
}