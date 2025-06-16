/**
 * 本文件功能: 公共JavaScript函数
 * 版权声明: 保留发行权和署名权
 * 作者信息: 15058593138@qq.com
 */

// 全局变量
var version = '1.0.0'; // 版本号
var searchTimer = null; // 搜索延时定时器

/**
 * AJAX请求函数
 * @param {Object} options 请求选项
 */
function ajaxRequest(options) {
    // 默认选项
    var defaults = {
        url: '',
        type: 'POST',
        data: {},
        dataType: 'json',
        success: function() {},
        error: function() {}
    };
    
    // 合并选项
    for (var key in options) {
        defaults[key] = options[key];
    }
    
    // 创建XHR对象
    var xhr = new XMLHttpRequest();
    
    // 处理请求参数
    var params = [];
    for (var key in defaults.data) {
        params.push(encodeURIComponent(key) + '=' + encodeURIComponent(defaults.data[key]));
    }
    var paramString = params.join('&');
    
    // 打开连接
    if (defaults.type.toUpperCase() === 'GET' && paramString) {
        defaults.url += (defaults.url.indexOf('?') > -1 ? '&' : '?') + paramString;
        paramString = '';
    }
    
    xhr.open(defaults.type, defaults.url, true);
    
    // 设置请求头
    if (defaults.type.toUpperCase() === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    
    // 发送请求
    xhr.send(paramString);
    
    // 处理响应
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var result;
                if (defaults.dataType === 'json') {
                    try {
                        result = JSON.parse(xhr.responseText);
                    } catch (e) {
                        defaults.error('解析JSON失败: ' + e.message);
                        return;
                    }
                } else {
                    result = xhr.responseText;
                }
                defaults.success(result);
            } else {
                defaults.error('请求失败: ' + xhr.status);
            }
        }
    };
}

/**
 * 获取表单数据
 * @param {String} formId 表单ID
 * @return {Object} 表单数据对象
 */
function getFormData(formId) {
    var form = document.getElementById(formId);
    if (!form) return {};
    
    var data = {};
    var elements = form.elements;
    
    for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        if (!element.name) continue;
        
        if (element.type === 'radio' || element.type === 'checkbox') {
            if (element.checked) {
                data[element.name] = element.value;
            }
        } else if (element.tagName === 'SELECT') {
            data[element.name] = element.value;
        } else if (element.type !== 'button' && element.type !== 'submit' && element.type !== 'reset') {
            data[element.name] = element.value;
        }
    }
    
    return data;
}

/**
 * 站点/线路输入提示函数
 * @param {String} inputId 输入框ID
 * @param {String} type 类型: line-线路, station-站点
 * @param {Function} callback 回调函数
 */
function searchSuggestion(inputId, type, callback) {
    var input = document.getElementById(inputId);
    if (!input) return;
    
    input.addEventListener('input', function() {
        var keyword = this.value.trim();
        if (!keyword) {
            hideSearchSuggestion();
            return;
        }
        
        // 清除之前的定时器
        if (searchTimer) {
            clearTimeout(searchTimer);
        }
        
        // 延时0.5秒执行搜索
        searchTimer = setTimeout(function() {
            ajaxRequest({
                url: 'index.php?act=suggest',
                data: {
                    keyword: keyword,
                    type: type
                },
                success: function(res) {
                    if (res.code === 0 && res.data && res.data.length > 0) {
                        showSearchSuggestion(inputId, res.data, callback);
                    } else {
                        hideSearchSuggestion();
                    }
                },
                error: function() {
                    hideSearchSuggestion();
                }
            });
        }, 500);
    });
}

/**
 * 显示搜索提示
 * @param {String} inputId 输入框ID
 * @param {Array} data 提示数据
 * @param {Function} callback 回调函数
 */
function showSearchSuggestion(inputId, data, callback) {
    var input = document.getElementById(inputId);
    if (!input) return;
    
    // 获取输入框位置
    var rect = input.getBoundingClientRect();
    
    // 移除旧的提示框
    hideSearchSuggestion();
    
    // 创建提示框
    var suggestion = document.createElement('div');
    suggestion.className = 'search-suggestion';
    suggestion.id = 'searchSuggestion';
    suggestion.style.top = (rect.bottom + window.scrollY) + 'px';
    suggestion.style.left = (rect.left + window.scrollX) + 'px';
    suggestion.style.width = rect.width + 'px';
    
    // 创建提示列表
    var ul = document.createElement('ul');
    
    // 最多显示10条记录
    var maxItems = Math.min(data.length, 10);
    
    for (var i = 0; i < maxItems; i++) {
        var li = document.createElement('li');
        li.textContent = data[i].name || data[i].zhan || data[i].text;
        li.setAttribute('data-id', data[i].id || data[i].zid || data[i].value);
        li.addEventListener('click', function() {
            input.value = this.textContent;
            if (callback) {
                callback(this.getAttribute('data-id'), this.textContent);
            }
            hideSearchSuggestion();
        });
        ul.appendChild(li);
    }
    
    suggestion.appendChild(ul);
    
    // 添加关闭按钮
    var closeBtn = document.createElement('div');
    closeBtn.className = 'close-btn';
    closeBtn.innerHTML = '×';
    closeBtn.addEventListener('click', hideSearchSuggestion);
    suggestion.appendChild(closeBtn);
    
    // 添加到页面
    document.body.appendChild(suggestion);
    
    // 点击页面其他区域关闭提示框
    document.addEventListener('click', function(e) {
        if (e.target !== input && !suggestion.contains(e.target)) {
            hideSearchSuggestion();
        }
    });
}

/**
 * 隐藏搜索提示
 */
function hideSearchSuggestion() {
    var suggestion = document.getElementById('searchSuggestion');
    if (suggestion) {
        suggestion.parentNode.removeChild(suggestion);
    }
}

/**
 * 分页函数
 * @param {Object} options 分页选项
 */
function pagination(options) {
    var defaults = {
        container: '',  // 容器ID
        currentPage: 1, // 当前页
        totalPages: 1,  // 总页数
        callback: function() {} // 回调函数
    };
    
    // 合并选项
    for (var key in options) {
        defaults[key] = options[key];
    }
    
    var container = document.getElementById(defaults.container);
    if (!container) return;
    
    container.innerHTML = '';
    
    // 如果总页数小于等于1，不显示分页
    if (defaults.totalPages <= 1) return;
    
    var html = '<div class="pagination">';
    
    // 起始页按钮
    if (defaults.currentPage > 1) {
        html += '<a href="javascript:;" data-page="1" class="page-btn first">起始页</a>';
    } else {
        html += '<span class="page-btn first disabled">起始页</span>';
    }
    
    // 上一页按钮
    if (defaults.currentPage > 1) {
        html += '<a href="javascript:;" data-page="' + (defaults.currentPage - 1) + '" class="page-btn prev">上一页</a>';
    } else {
        html += '<span class="page-btn prev disabled">上一页</span>';
    }
    
    // 页码下拉选择
    html += '<select class="page-select">';
    for (var i = 1; i <= defaults.totalPages; i++) {
        html += '<option value="' + i + '"' + (i == defaults.currentPage ? ' selected' : '') + '>' + i + '</option>';
    }
    html += '</select>';
    
    // 下一页按钮
    if (defaults.currentPage < defaults.totalPages) {
        html += '<a href="javascript:;" data-page="' + (defaults.currentPage + 1) + '" class="page-btn next">下一页</a>';
    } else {
        html += '<span class="page-btn next disabled">下一页</span>';
    }
    
    // 最后页按钮
    if (defaults.currentPage < defaults.totalPages) {
        html += '<a href="javascript:;" data-page="' + defaults.totalPages + '" class="page-btn last">最后页</a>';
    } else {
        html += '<span class="page-btn last disabled">最后页</span>';
    }
    
    html += '</div>';
    
    container.innerHTML = html;
    
    // 绑定页码点击事件
    var btns = container.querySelectorAll('.page-btn:not(.disabled)');
    for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener('click', function() {
            var page = parseInt(this.getAttribute('data-page'));
            defaults.callback(page);
        });
    }
    
    // 绑定下拉选择事件
    var select = container.querySelector('.page-select');
    if (select) {
        select.addEventListener('change', function() {
            var page = parseInt(this.value);
            defaults.callback(page);
        });
    }
}

/**
 * 显示遮罩层
 * @param {String} title 标题
 * @param {String} content 内容
 * @param {Array} buttons 按钮配置
 */
function showModal(title, content, buttons) {
    // 移除旧的遮罩层
    hideModal();
    
    // 创建遮罩层
    var modal = document.createElement('div');
    modal.className = 'modal';
    modal.id = 'modal';
    
    // 创建遮罩层内容
    var modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    
    // 标题
    var modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header';
    modalHeader.innerHTML = '<h3>' + title + '</h3>';
    
    // 关闭按钮
    var closeBtn = document.createElement('span');
    closeBtn.className = 'modal-close';
    closeBtn.innerHTML = '×';
    closeBtn.addEventListener('click', hideModal);
    modalHeader.appendChild(closeBtn);
    
    // 内容
    var modalBody = document.createElement('div');
    modalBody.className = 'modal-body';
    modalBody.innerHTML = content;
    
    // 按钮
    var modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer';
    
    if (buttons && buttons.length > 0) {
        for (var i = 0; i < buttons.length; i++) {
            var btn = document.createElement('button');
            btn.className = 'btn ' + (buttons[i].class || '');
            btn.textContent = buttons[i].text || '确定';
            
            if (buttons[i].callback) {
                (function(callback) {
                    btn.addEventListener('click', function() {
                        callback();
                    });
                })(buttons[i].callback);
            } else {
                btn.addEventListener('click', hideModal);
            }
            
            modalFooter.appendChild(btn);
        }
    } else {
        // 默认按钮
        var defaultBtn = document.createElement('button');
        defaultBtn.className = 'btn';
        defaultBtn.textContent = '确定';
        defaultBtn.addEventListener('click', hideModal);
        modalFooter.appendChild(defaultBtn);
    }
    
    // 组装遮罩层
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);
    modal.appendChild(modalContent);
    
    // 添加到页面
    document.body.appendChild(modal);
    
    // 禁止页面滚动
    document.body.style.overflow = 'hidden';
    
    // 显示遮罩层
    setTimeout(function() {
        modal.style.opacity = '1';
    }, 10);
}

/**
 * 隐藏遮罩层
 */
function hideModal() {
    var modal = document.getElementById('modal');
    if (modal) {
        modal.parentNode.removeChild(modal);
        document.body.style.overflow = '';
    }
}

/**
 * 显示Toast消息
 * @param {String} message 消息内容
 * @param {Number} duration 持续时间，单位毫秒
 */
function showToast(message, duration) {
    duration = duration || 2000;
    
    // 移除旧的Toast
    hideToast();
    
    // 创建Toast
    var toast = document.createElement('div');
    toast.className = 'toast';
    toast.id = 'toast';
    toast.textContent = message;
    
    // 添加到页面
    document.body.appendChild(toast);
    
    // 显示Toast
    setTimeout(function() {
        toast.style.opacity = '1';
        toast.style.transform = 'translate(-50%, 0)';
    }, 10);
    
    // 设置定时关闭
    setTimeout(hideToast, duration);
}

/**
 * 隐藏Toast
 */
function hideToast() {
    var toast = document.getElementById('toast');
    if (toast) {
        toast.parentNode.removeChild(toast);
    }
}

/**
 * Tab切换函数
 * @param {String} container Tab容器ID
 */
function initTabs(container) {
    var tabContainer = document.getElementById(container);
    if (!tabContainer) return;
    
    var tabs = tabContainer.querySelectorAll('.tab-header .tab');
    var contents = tabContainer.querySelectorAll('.tab-content .tab-pane');
    
    for (var i = 0; i < tabs.length; i++) {
        (function(index) {
            tabs[index].addEventListener('click', function() {
                // 移除所有active类
                for (var j = 0; j < tabs.length; j++) {
                    tabs[j].classList.remove('active');
                    if (contents[j]) {
                        contents[j].classList.remove('active');
                    }
                }
                
                // 添加active类
                this.classList.add('active');
                if (contents[index]) {
                    contents[index].classList.add('active');
                }
            });
        })(i);
    }
    
    // 确保页面加载时Tab初始化正确
    var activeTab = tabContainer.querySelector('.tab-header .tab.active');
    if (activeTab) {
        var activeIndex = Array.from(tabs).indexOf(activeTab);
        if (activeIndex >= 0 && contents[activeIndex]) {
            for (var k = 0; k < contents.length; k++) {
                contents[k].classList.remove('active');
            }
            contents[activeIndex].classList.add('active');
        }
    } else if (tabs.length > 0) {
        // 如果没有激活的标签，默认选中第一个
        tabs[0].classList.add('active');
        if (contents[0]) {
            contents[0].classList.add('active');
        }
    }
}
