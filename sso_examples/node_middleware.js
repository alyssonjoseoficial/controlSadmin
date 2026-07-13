/**
 * Exemplo de Middleware SSO para sistemas SaaS feitos em Node.js.
 * Necessita instalar a biblioteca jsonwebtoken (npm install jsonwebtoken).
 * 
 * Exemplo de integração no Express.js:
 */

const express = require('express');
const jwt = require('jsonwebtoken');
const app = express();

// A MESMA chave secreta cadastrada para este sistema no BD do Control_SADMIN
const SECRET_KEY = 'sys3_secret_super_safe_123!@#';

app.get('/admin/sso', (req, res) => {
    const token = req.query.token;

    if (!token) {
        return res.status(401).send('Erro SSO: Token ausente.');
    }

    try {
        // O método verify já checa a assinatura e a expiração (exp) automaticamente.
        const decoded = jwt.verify(token, SECRET_KEY);
        
        // TOKEN VÁLIDO E AUTÊNTICO!
        
        // Aqui você cria a sessão local que o seu SaaS em Node exige.
        // Exemplo se estiver usando express-session:
        // req.session.admin_logged_in = true;
        // req.session.admin_id = decoded.user_id;
        
        // Redireciona para o painel interno do seu SaaS Node.js
        res.redirect('/admin/dashboard');
    } catch (err) {
        // Se o token expirou (passou dos 30s) ou a chave for inválida, cai aqui.
        res.status(401).send(`Falha na autenticação SSO: ${err.message}`);
    }
});

// Outras rotas do seu sistema Node...
// app.listen(3000, () => console.log('SaaS rodando na porta 3000'));
