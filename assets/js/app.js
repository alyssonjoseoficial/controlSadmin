// app.js
document.addEventListener('DOMContentLoaded', () => {
    
    // Elements
    const loginScreen = document.getElementById('login-screen');
    const dashboardScreen = document.getElementById('dashboard-screen');
    const loginForm = document.getElementById('login-form');
    const errorMsg = document.getElementById('login-error');
    const systemsGrid = document.getElementById('systems-grid');
    const onlineCount = document.getElementById('online-count');
    const adminNameDisplay = document.getElementById('admin-name');
    const logoutBtn = document.getElementById('logout-btn');
    const ssoLoading = document.getElementById('sso-loading');

    // Check if already logged in (simulated with localStorage for this frontend demo)
    const storedUser = localStorage.getItem('sadmin_user');
    if (storedUser) {
        const user = JSON.parse(storedUser);
        showDashboard(user);
    }

    // Login Logic
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const btn = loginForm.querySelector('button');
        
        btn.innerHTML = 'Autenticando...';
        btn.disabled = true;
        errorMsg.innerText = '';

        try {
            const response = await fetch('api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                localStorage.setItem('sadmin_user', JSON.stringify(data.user));
                showDashboard(data.user);
            } else {
                errorMsg.innerText = data.message || 'Falha na autenticação.';
            }
        } catch (error) {
            errorMsg.innerText = 'Erro ao conectar com o servidor.';
        } finally {
            btn.innerHTML = 'Acessar HUB Central';
            btn.disabled = false;
        }
    });

    // Logout
    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem('sadmin_user');
        dashboardScreen.classList.remove('active');
        setTimeout(() => {
            loginScreen.classList.add('active');
        }, 500);
    });

    function showDashboard(user) {
        adminNameDisplay.innerText = user.name;
        loginScreen.classList.remove('active');
        setTimeout(() => {
            dashboardScreen.classList.add('active');
            loadSystems();
        }, 500);
    }

    // Load Systems and Render Cards
    async function loadSystems() {
        try {
            const response = await fetch('api/dashboard/stats.php');
            const data = await response.json();

            if (data.success && data.systems) {
                renderCards(data.systems);
                onlineCount.innerText = data.systems.length;
            }
        } catch (error) {
            console.error('Erro ao carregar sistemas:', error);
            systemsGrid.innerHTML = '<p style="color:var(--danger)">Erro ao carregar dados do painel.</p>';
        }
    }

    function renderCards(systems) {
        systemsGrid.innerHTML = '';
        
        systems.forEach((sys, index) => {
            // Micro-animation delay staggered
            const animDelay = index * 0.1;
            
            const card = document.createElement('div');
            card.className = 'saas-card glass-panel';
            card.style.animation = `fadeInUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) ${animDelay}s forwards`;
            card.style.opacity = '0'; // For animation
            card.style.transform = 'translateY(20px)';
            
            let badgeHtml = sys.notifications > 0 ? `<div class="badge">${sys.notifications}</div>` : '';

            card.innerHTML = `
                ${badgeHtml}
                <div class="card-header">
                    <div class="logo-wrapper" onclick="event.stopPropagation(); document.getElementById('upload-logo-${sys.id}').click();" title="Clique para trocar a logo">
                        <img src="${sys.logo_url}" alt="${sys.name}" class="card-logo" id="logo-img-${sys.id}">
                        <div class="logo-overlay"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-camera"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></div>
                        <input type="file" id="upload-logo-${sys.id}" style="display:none" accept="image/*" onchange="uploadLogo(event, ${sys.id})">
                    </div>
                    <div class="card-title">
                        <h3>${sys.name}</h3>
                        <span class="status">● Online</span>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item" title="Usuários Ativos">
                        <span class="val">${sys.active_users}</span>
                        <span class="lbl">Usuários</span>
                    </div>
                    <div class="stat-item" title="Faturamento Mensal">
                        <span class="val">R$ ${sys.revenue_monthly}</span>
                        <span class="lbl">Rec. Mensal</span>
                    </div>
                    <div class="stat-item" title="Bloqueios Automáticos (Hoje)">
                        <span class="val">${sys.auto_blocks_today}</span>
                        <span class="lbl">Bloqueios</span>
                    </div>
                    <div class="stat-item" title="Novas Assinaturas (Hoje)">
                        <span class="val">${sys.new_subscriptions_today}</span>
                        <span class="lbl">Assinaturas</span>
                    </div>
                </div>
                <button class="sso-btn" data-id="${sys.id}">Acessar Painel</button>
                <div style="font-size: 10px; color: #666; text-align: center; margin-top: 5px;">Última sync: ${sys.last_sync || 'Nunca'}</div>
            `;

            // O Card inteiro clica, mas podemos focar no botão SSO também
            card.addEventListener('click', () => initiateSSO(sys.id, sys.name));

            systemsGrid.appendChild(card);
        });
    }

    // SSO Logic
    async function initiateSSO(systemId, systemName) {
        ssoLoading.querySelector('p').innerText = `Conectando ao ${systemName}...`;
        ssoLoading.classList.add('active');

        try {
            const response = await fetch(`api/sso/generate_token.php?system_id=${systemId}`);
            const data = await response.json();

            if (response.ok && data.success) {
                // Abre o painel do sistema filho em uma nova aba para não perder o contexto do HUB
                window.open(data.redirect_url, '_blank');
            } else {
                alert(`Erro SSO: ${data.message}`);
            }
        } catch (error) {
            alert('Erro de rede ao tentar o SSO.');
        } finally {
            setTimeout(() => {
                ssoLoading.classList.remove('active');
            }, 800); 
        }
    }

    // Navigation Logic
    const navDashboard = document.getElementById('nav-dashboard');
    const navAccess = document.getElementById('nav-access');
    const viewDashboard = document.getElementById('view-dashboard');
    const viewAccess = document.getElementById('view-access');

    if (navDashboard && navAccess) {
        navDashboard.addEventListener('click', (e) => {
            e.preventDefault();
            navDashboard.parentElement.classList.add('active');
            navAccess.parentElement.classList.remove('active');
            viewDashboard.style.display = 'block';
            viewAccess.style.display = 'none';
        });

        navAccess.addEventListener('click', (e) => {
            e.preventDefault();
            navAccess.parentElement.classList.add('active');
            navDashboard.parentElement.classList.remove('active');
            viewDashboard.style.display = 'none';
            viewAccess.style.display = 'block';
            loadAccessData();
        });
    }

    async function loadAccessData() {
        try {
            const response = await fetch('api/access/get_data.php');
            const data = await response.json();
            if (data.success) {
                // Populate Users
                const usersTable = document.querySelector('#users-table tbody');
                usersTable.innerHTML = data.users.length ? data.users.map(u => `
                    <tr>
                        <td>${u.id}</td>
                        <td>${u.name}</td>
                        <td>${u.email}</td>
                        <td>${u.created_at}</td>
                        <td><button class="action-btn" onclick="alert('Editar Admin ID ${u.id}')">Editar</button></td>
                    </tr>
                `).join('') : '<tr><td colspan="5" style="text-align:center;">Nenhum usuário encontrado.</td></tr>';

                // Populate SaaS
                const saasTable = document.querySelector('#saas-table tbody');
                saasTable.innerHTML = data.systems.length ? data.systems.map(s => `
                    <tr>
                        <td>${s.id}</td>
                        <td>${s.name}</td>
                        <td>${s.slug}</td>
                        <td style="font-family: monospace;">${s.api_key}</td>
                        <td><button class="action-btn" onclick="alert('Chave Secreta: ${s.secret_key}')">Ver Secret</button></td>
                    </tr>
                `).join('') : '<tr><td colspan="5" style="text-align:center;">Nenhum sistema encontrado.</td></tr>';
            }
        } catch (error) {
            console.error('Erro ao carregar dados de acesso:', error);
            document.querySelector('#users-table tbody').innerHTML = '<tr><td colspan="5" style="text-align:center; color:red;">Erro ao carregar dados.</td></tr>';
            document.querySelector('#saas-table tbody').innerHTML = '<tr><td colspan="5" style="text-align:center; color:red;">Erro ao carregar dados.</td></tr>';
        }
    }
});

// Add Keyframe dynamically
const style = document.createElement('style');
style.innerHTML = `
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// Upload Logo Function
window.uploadLogo = async function(event, systemId) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('system_id', systemId);
    formData.append('logo', file);

    const imgElement = document.getElementById(`logo-img-${systemId}`);
    const originalSrc = imgElement.src;
    imgElement.style.opacity = '0.5';

    try {
        const response = await fetch('api/dashboard/upload_logo.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            imgElement.src = data.logo_url + '?t=' + new Date().getTime(); // Prevent caching
        } else {
            alert('Erro ao enviar logo: ' + data.message);
            imgElement.src = originalSrc;
        }
    } catch (err) {
        alert('Erro de conexão ao tentar fazer o upload.');
        imgElement.src = originalSrc;
    } finally {
        imgElement.style.opacity = '1';
    }
}
