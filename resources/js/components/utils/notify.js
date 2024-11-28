export const notify = (options) => {
    const { group = 'generic', title = '', text = '' } = options;
    if (window.$notify) {
        window.$notify({
            group,
            title,
            text
        });
    }
};