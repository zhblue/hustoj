/* HUSTOJ · Luogu-style theme interactions */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        var body = document.body;
        var user = document.getElementById('lg-user');
        var userBtn = user ? user.querySelector('.lg-user-btn') : null;
        var mask = document.getElementById('lg-mask');
        var moreBtn = document.getElementById('lg-more-btn');
        var side = document.getElementById('lg-side');

        function closeUserMenu() {
            if (!user) return;
            user.classList.remove('is-open');
            if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
        }

        if (user && userBtn) {
            userBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = user.classList.toggle('is-open');
                userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });

            user.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        document.addEventListener('click', function () {
            closeUserMenu();
            closeDrawer();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeUserMenu();
                closeDrawer();
            }
        });

        function openDrawer() {
            body.classList.add('lg-drawer-open');
        }

        function closeDrawer() {
            body.classList.remove('lg-drawer-open');
        }

        if (moreBtn) {
            moreBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                body.classList.toggle('lg-drawer-open');
            });
        }

        if (mask) {
            mask.addEventListener('click', function () {
                closeDrawer();
            });
        }

        if (side) {
            side.addEventListener('click', function (e) {
                if (e.target.closest('a')) closeDrawer();
            });
        }

        if (window.matchMedia) {
            var mq = window.matchMedia('(min-width: 901px)');
            var onChange = function (e) {
                if (e.matches) closeDrawer();
            };
            if (mq.addEventListener) mq.addEventListener('change', onChange);
            else if (mq.addListener) mq.addListener(onChange);
        }

        /* keep the menu closed after using a dropdown link */
        if (user) {
            user.querySelectorAll('.lg-user-menu a').forEach(function (a) {
                a.addEventListener('click', closeUserMenu);
            });
        }

        /* highlight the current top-level page when there is no server flag */
        if (!body.getAttribute('data-page')) {
            var path = window.location.pathname.replace(/\/+$/, '');
            var file = path.substring(path.lastIndexOf('/') + 1) || 'index.php';
            var map = {
                'index.php': 'home',
                'problemset.php': 'problems',
                'problem.php': 'problems',
                'category.php': 'problems',
                'contest.php': 'contests',
                'contestset.php': 'contests',
                'recent-contest.php': 'contests',
                'status.php': 'status',
                'ranklist.php': 'rank',
                'discuss.php': 'discuss',
                'faqs.php': 'discuss',
                'viewnews.php': 'discuss'
            };
            var page = map[file] || '';
            if (page) {
                body.setAttribute('data-page', page);
                document.querySelectorAll('#lg-side .lg-nav-item').forEach(function (el) {
                    el.classList.toggle('is-active', el.getAttribute('href') && el.getAttribute('href').indexOf(file) !== -1);
                });
            }
        }
    });
})();
