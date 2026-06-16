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
    const oldNotification = document.querySelector('.custom-notification');
    if (oldNotification) oldNotification.remove();
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} custom-notification`;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i> ${message}`;
    
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

// ============ LOGOUT FUNCTION ============
function logout() {
    sessionStorage.removeItem('user');
    
    fetch(`${API_BASE}/auth/logout.php`, { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    }).catch(() => {});
    
    window.location.href = '/web_dat_lich_san_bong/public/login.html';
}

// ============ FILTER PITCHES ============
function filterPitches() {
    const searchValue = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const priceFilter = document.getElementById('priceFilter')?.value || 'all';
    const cards = document.querySelectorAll('.pitch-card');
    
    cards.forEach(card => {
        const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const priceText = card.querySelector('.price')?.textContent || '';
        const price = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;
        
        let priceMatch = true;
        if (priceFilter === 'under300') priceMatch = price < 300000;
        else if (priceFilter === '300-500') priceMatch = price >= 300000 && price <= 500000;
        else if (priceFilter === 'over500') priceMatch = price > 500000;
        
        const searchMatch = title.includes(searchValue);
        
        if (searchMatch && priceMatch) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// ============ LOAD STATS FOR HOME ============
async function loadHomeStats() {
    try {
        const bookings = await apiCall('/bookings/list.php');
        const today = new Date().toISOString().split('T')[0];
        const todayBookings = bookings.bookings?.filter(b => b.booking_date === today).length || 0;
        
        const todaySpan = document.getElementById('todayBookings');
        if (todaySpan) todaySpan.textContent = todayBookings;
    } catch(e) {}
}

// ============ CSS ANIMATION ============
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);