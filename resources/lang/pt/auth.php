<?php

return [
    // Mensagens de autenticação
    'login' => 'Entrar',
    'register' => 'Criar Conta',
    'logout' => 'Sair',
    'remember_me' => 'Manter sessão ativa',
    'forgot_password' => 'Esqueceu sua senha?',
    'reset_password' => 'Redefinir Senha',
    'send_reset_link' => 'Enviar Link de Redefinição',
    'click_reset_link' => 'Clique aqui para redefinir sua senha',
    'verify_email' => 'Verificar E-mail',
    'verify_email_sent' => 'Um novo link de verificação foi enviado para seu e-mail.',
    'verify_email_notice' => 'Antes de continuar, verifique seu e-mail com o link que enviamos.',
    'verify_email_success' => 'Seu e-mail foi verificado com sucesso.',

    // Mensagens de senha
    'change_password' => 'Alterar Senha',
    'change_password_description' => 'Por favor, digite sua nova senha para continuar.',
    'current_password' => 'Senha Atual',
    'new_password' => 'Nova Senha',
    'confirm_password' => 'Confirmar Senha',
    'password_requirements' => 'A senha deve ter pelo menos 8 caracteres, uma maiúscula, uma minúscula, um número e um símbolo.',
    'password_current_wrong' => 'A senha atual está incorreta.',
    'password_change_success' => 'Sua senha foi alterada com sucesso.',
    'password_change_error' => 'Ocorreu um erro ao alterar a senha.',

    // Mensagens de sucesso/erro
    'login_success' => 'Bem-vindo de volta! Você entrou com sucesso.',
    'login_error' => 'E-mail ou senha incorretos. Verifique seus dados.',
    'login_error_google_account' => 'Esta conta foi criada com Google. Entre usando o botão "Continuar com Google" ou defina uma senha no seu perfil.',
    'account_inactive' => 'Sua conta está inativa. Entre em contato com o administrador para ativá-la.',
    'register_success' => 'Conta criada com sucesso! Verifique seu e-mail para ativar sua conta.',
    'register_success_title' => 'Registro Bem-sucedido!',
    'logout_success' => 'Você saiu com sucesso. Até breve!',
    'welcome' => 'Bem-vindo ao 4GMovil!',
    'goodbye' => 'Obrigado por usar o 4GMovil!',

    // Mensagens de validação
    'validation' => [
        'nombre_usuario' => [
            'required' => 'Nome de usuário é obrigatório.',
            'max' => 'Nome de usuário não pode ter mais de :max caracteres.',
            'regex' => 'Nome de usuário pode conter apenas letras e espaços.',
        ],
        'correo_electronico' => [
            'required' => 'E-mail é obrigatório.',
            'email' => 'Por favor, digite um e-mail válido.',
            'unique' => 'Este e-mail já está registrado em nosso sistema.',
        ],
        'telefono' => [
            'required' => 'Número de telefone é obrigatório.',
            'regex' => 'Telefone deve ter 10 dígitos e começar com 3.',
        ],
        'contrasena' => [
            'required' => 'Senha é obrigatória.',
            'min' => 'Senha deve ter pelo menos :min caracteres.',
            'confirmed' => 'As senhas não coincidem.',
            'regex' => 'Senha deve atender aos requisitos de segurança.',
        ],
        'acepta_terminos' => [
            'accepted' => 'Você deve aceitar os termos e condições para continuar.',
        ],
    ],

    // Mensagens de verificação
    'verification' => [
        'required' => 'Por favor, verifique seu e-mail antes de continuar.',
        'sent' => 'Um novo link de verificação foi enviado para seu e-mail.',
        'verified' => 'Seu e-mail foi verificado com sucesso.',
        'already_verified' => 'Seu e-mail já foi verificado.',
    ],

    // Mensagens de segurança
    'security' => [
        'password_requirements' => 'Sua senha deve atender aos requisitos de segurança para proteger sua conta.',
        'account_locked' => 'Sua conta foi bloqueada temporariamente devido a múltiplas tentativas falhas.',
        'too_many_attempts' => 'Muitas tentativas falhas. Tente novamente em :minutes minutos.',
        'session_expired' => 'Sua sessão expirou. Faça login novamente.',
    ],
]; 
