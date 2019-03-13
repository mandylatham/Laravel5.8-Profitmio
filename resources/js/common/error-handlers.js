window.PmEvent.listen('errors.api', (error) => {
    app.$swal({
        type: 'error',
        title: 'Error',
        html: error + '<br>You can contact our support to address this issue.',
        footer: `<a href="mailto:${window.emails.support}">Contact support</a>`
    });
});
