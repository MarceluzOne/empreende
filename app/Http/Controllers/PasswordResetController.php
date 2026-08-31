<?php

namespace App\Http\Controllers;

/**
 * Página de orientação do "Esqueci minha senha".
 *
 * O envio por e-mail nunca foi ligado, e uma tela que aceita o endereço e diz
 * "enviamos o link" sem nada chegar é pior que não existir. Aqui a pessoa é
 * direcionada ao atendimento — presencial ou pelo WhatsApp —, onde um
 * funcionário gera o link pelo AssistedPasswordResetService.
 */
class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password', ['whatsapp' => $this->supportWhatsapp()]);
    }

    /**
     * O contexto do site é resolvido por middleware e pode não existir (rota
     * fora de qualquer site); daí o bound() antes de pedir ao container.
     */
    private function supportWhatsapp(): ?string
    {
        $site = app()->bound('current.site.config') ? app('current.site.config') : [];

        return $site['support_whatsapp'] ?? null;
    }
}
