<?php

return [
    // Mensajes generales de formulario
    'required_field' => 'Este campo é obrigatório',
    'invalid_format' => 'O formato é inválido',
    'fix_errors' => 'Por favor, corrija os erros antes de continuar',
    'validation_error' => 'Erro de validação',
    'success' => 'Operação bem-sucedida',
    'error' => 'Ocorreu um erro',
    'loading' => 'Carregando...',
    'processing' => 'Processando a solicitação...',
    'confirm_action' => 'Tem certeza de que deseja realizar esta ação?',
    'yes' => 'Sim',
    'no' => 'Não',
    'cancel' => 'Cancelar',
    'save' => 'Salvar',
    'close' => 'Fechar',
    'back' => 'Voltar',
    'continue' => 'Continuar',

    // Mensajes de estado
    'status' => [
        'success' => 'Operação concluída com sucesso',
        'error' => 'Ocorreu um erro ao processar a solicitação',
        'warning' => 'Há alguns problemas que requerem sua atenção',
        'info' => 'Informação importante',
        'loading' => 'Por favor, aguarde...',
        'saving' => 'Salvando alterações...',
        'updating' => 'Atualizando informações...',
        'deleting' => 'Excluindo...',
        'session_expired' => 'Sua sessão expirou, faça login novamente',
    ],

    // Mensajes específicos de campos
    'name' => [
        'required' => 'O nome é obrigatório',
        'format' => 'O nome deve ter entre 2 e 25 caracteres e conter apenas letras',
        'invalid_chars' => 'O nome contém caracteres não permitidos',
        'too_short' => 'O nome é muito curto (mínimo de 2 caracteres)',
        'too_long' => 'O nome é muito longo (máximo de 25 caracteres)',
    ],
    'email' => [
        'required' => 'O e-mail é obrigatório',
        'format' => 'O formato do e-mail é inválido',
        'unique' => 'Este e-mail já está registrado',
        'not_found' => 'Nenhuma conta foi encontrada com este e-mail',
        'verified' => 'E-mail verificado com sucesso',
        'verification_sent' => 'Um e-mail de verificação foi enviado',
    ],
    'phone' => [
        'required' => 'O telefone é obrigatório',
        'format' => 'O telefone deve ter exatamente 10 dígitos',
        'invalid' => 'O número de telefone é inválido',
        'unique' => 'Este número de telefone já está registrado',
    ],
    'password' => [
        'required' => 'A senha é obrigatória',
        'min_length' => 'A senha deve ter pelo menos 8 caracteres',
        'uppercase' => 'A senha deve conter pelo menos uma letra maiúscula',
        'lowercase' => 'A senha deve conter pelo menos uma letra minúscula',
        'number' => 'A senha deve conter pelo menos um número',
        'symbol' => 'A senha deve conter pelo menos um símbolo',
        'mismatch' => 'As senhas não coincidem',
        'current_wrong' => 'A senha atual está incorreta',
        'recently_used' => 'Você não pode usar uma senha utilizada recentemente',
        'requirements' => 'A senha deve atender aos seguintes requisitos:',
        'strength' => [
            'weak' => 'Fraca',
            'medium' => 'Média',
            'strong' => 'Forte',
            'very_strong' => 'Muito forte',
        ],
    ],
    'terms' => [
        'required' => 'Você deve aceitar os termos e condições',
        'updated' => 'Os termos e condições foram atualizados',
        'read_more' => 'Ler mais',
    ],

    // Mensajes de autenticación
    'auth' => [
        'welcome' => 'Bem-vindo de volta!',
        'goodbye' => 'Até breve!',
        'login_success' => 'Você entrou com sucesso',
        'login_error' => 'Erro ao fazer login',
        'logout_success' => 'Você saiu com sucesso',
        'invalid_credentials' => 'As credenciais fornecidas estão incorretas',
        'account_locked' => 'Sua conta foi bloqueada temporariamente',
        'too_many_attempts' => 'Muitas tentativas falhas. Tente novamente em :minutes minutos',
        'remember_me' => 'Lembrar de mim',
        'forgot_password' => 'Esqueceu sua senha?',
        'reset_password' => 'Redefinir senha',
        'reset_password_success' => 'Sua senha foi redefinida com sucesso',
    ],

    // Mensajes de registro
    'register' => [
        'success' => 'Registro concluído com sucesso',
        'error' => 'Erro ao concluir o registro',
        'verification_required' => 'Por favor, verifique seu e-mail',
        'already_registered' => 'Você já tem uma conta registrada',
        'complete_profile' => 'Complete seu perfil',
    ],

    // Mensajes de perfil
    'profile' => [
        'updated' => 'Perfil atualizado com sucesso',
        'update_error' => 'Erro ao atualizar o perfil',
        'photo_updated' => 'Foto do perfil atualizada',
        'photo_error' => 'Erro ao atualizar a foto do perfil',
        'delete_account' => 'Excluir conta',
        'delete_confirm' => 'Tem certeza de que deseja excluir sua conta? Esta ação não pode ser desfeita',
    ],

    // Mensajes de errores comunes
    'errors' => [
        'default' => 'Ocorreu um erro inesperado',
        'connection' => 'Erro de conexão',
        'timeout' => 'A solicitação excedeu o tempo limite',
        'validation' => 'Verifique os dados informados',
        'server' => 'Erro no servidor',
        'not_found' => 'Não encontrado',
        'forbidden' => 'Acesso negado',
        'unauthorized' => 'Não autorizado',
    ],
]; 