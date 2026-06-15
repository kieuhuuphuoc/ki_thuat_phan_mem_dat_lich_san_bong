// API Base URL
const API_BASE = '/web_dat_lich_san_bong/api';

// ============ API CALL FUNCTION ============
async function apiCall(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json'
        },
        ...options
    };
    
    if (options.body && typeof options.body === 'object') {
        defaultOptions.body = JSON.stringify(options.body);
    }
    
    try {
        const response = await fetch(`${API_BASE}${endpoint}`, defaultOptions);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Có lỗi xảy ra');
        }
        
        return data;
    } catch (error) {
        showNotification(error.message, 'error');
        throw error;
    }
}

// ============ SHOW NOTIFICATION ============
function showNotification(message, type = 'info') {
    // Xóa thông báo cũ nếu có
    const oldNotification = document.querySelector('.custom-notification');
    if (oldNotification) oldNotification.remove();
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} custom-notification`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i> ${message}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 12px 20px;
        border-radius: 8px;
        background: ${type === 'success' ? '#48bb78' : type === 'error' ? '#f56565' : '#4299e1'};
        color: white;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// ============ FORMAT CURRENCY ============
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// ============ FORMAT DATE ============
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// ============ ESCAPE HTML ============
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============ GET CURRENT USER ============
function getCurrentUser() {
    const user = sessionStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

// ============ CHECK AUTHENTICATION ============
function isAuthenticated() {
    return sessionStorage.getItem('user') !== null;
}

// ============ LOGOUT FUNCTION - SỬA LẠI ============
function logout() {
    // Xóa user khỏi sessionStorage
    sessionStorage.removeItem('user');
    
    // Gọi API logout
    fetch(`${API_BASE}/auth/logout.php`, { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(() => {
        // Chuyển hướng về trang đăng nhập
        window.location.href = '/web_dat_lich_san_bong/public/login.html';
    })
    .catch(() => {
        // Nếu lỗi vẫn chuyển hướng
        window.location.href = '/web_dat_lich_san_bong/public/login.html';
    });
}

// ============ CSS ANIMATION ============
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);