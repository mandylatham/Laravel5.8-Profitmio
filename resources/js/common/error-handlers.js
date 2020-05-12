/**
 * REST API response error handler
 */
window.PmEvent.listen('errors.api', (error) => {
    app.$swal({
        type: 'error',
        title: error,
        html:'You can contact our support to address this issue.',
        footer: `<a href="mailto:${window.emails.support}">Contact support</a>`
    });
});

/**
 * Session timeout error handler
 */
window.PmEvent.listen('errors.sessionTimeout', (error) => {
    app.$swal({
        type: 'warning',
        title: 'Session expired',
        text: 'You session has been expired, please re-login.',
        confirmButtonText: 'Login',
        focusConfirm: false,
    }).then(() => window.location.href = '/login');
});
