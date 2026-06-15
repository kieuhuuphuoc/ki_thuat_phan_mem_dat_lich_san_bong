// Xử lý đăng nhập
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    try {
        const data = await apiCall('/auth/login.php', {
            method: 'POST',
            body: { email, password }
        });
        
        if (data.success) {
            sessionStorage.setItem('user', JSON.stringify(data.user));
            showNotification('Đăng nhập thành công!', 'success');
            
            if (data.user.role === 'admin') {
                window.location.href = '/web_dat_lich_san_bong/public/assets/admin/index.html';
            } else {
                window.location.href = '/web_dat_lich_san_bong/public/index.html';
            }
        }
    } catch (error) {
        console.error('Login error:', error);
    }
}

// Xử lý đăng ký
async function handleRegister(event) {
    event.preventDefault();
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (password !== confirm) {
        showNotification('Mật khẩu xác nhận không khớp!', 'error');
        return;
    }
    
    try {
        const data = await apiCall('/auth/register.php', {
            method: 'POST',
            body: { name, email, password }
        });
        
        if (data.success) {
            showNotification('Đăng ký thành công!', 'success');
            setTimeout(() => {
                window.location.href = '/web_dat_lich_san_bong/public/login.html';
            }, 1500);
        }
    } catch (error) {
        console.error('Register error:', error);
    }
}

// Kiểm tra đăng nhập
function checkAuth() {
    const user = getCurrentUser();
    const currentPage = window.location.pathname.split('/').pop();
    const protectedPages = ['index.html', 'pitches.html', 'booking.html', 'my-bookings.html'];
    
    if (!user && protectedPages.includes(currentPage)) {
        window.location.href = '/web_dat_lich_san_bong/public/login.html';
    }
    
    // Hiển thị tên user
    const userNameElements = document.querySelectorAll('.user-name');
    if (user) {
        userNameElements.forEach(el => el.textContent = user.name);
    }
}

// Khởi tạo
document.addEventListener('DOMContentLoaded', () => {
    checkAuth();
    
    const loginForm = document.getElementById('login-form');
    if (loginForm) loginForm.addEventListener('submit', handleLogin);
    
    const registerForm = document.getElementById('register-form');
    if (registerForm) registerForm.addEventListener('submit', handleRegister);
    
    // Xử lý nút logout - TÌM THEO ID
    const logoutBtn = document.getElementById('logout-link');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            logout();
        });
    }
    
    // Xử lý nút logout trong admin
    const adminLogoutBtn = document.getElementById('logoutBtn');
    if (adminLogoutBtn) {
        adminLogoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            logout();
        });
    }
});