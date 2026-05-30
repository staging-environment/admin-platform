<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Antigravity FilePond sorter: DOMContentLoaded fired');
        let attempts = 0;
        const checkInterval = setInterval(() => {
            attempts++;
            if (window.FilePond) {
                console.log('Antigravity FilePond sorter: FilePond found after ' + attempts + ' attempts');
                clearInterval(checkInterval);
                
                const getFilename = (url) => {
                    if (typeof url !== 'string') return '';
                    return url.split('/').pop().split('?')[0];
                };

                const getItemFilename = (item) => {
                    if (!item) return '';
                    if (item.filename) return item.filename;
                    if (item.file && item.file.name) return item.file.name;
                    if (typeof item.serverId === 'string') return getFilename(item.serverId);
                    if (typeof item.source === 'string') return getFilename(item.source);
                    return '';
                };

                const getOriginalFilename = (f) => {
                    if (!f) return '';
                    if (typeof f === 'string') return getFilename(f);
                    if (f && typeof f.source === 'string') return getFilename(f.source);
                    return '';
                };
                
                const originalCreate = window.FilePond.create;
                window.FilePond.create = function(element, options) {
                    console.log('Antigravity FilePond sorter: FilePond.create called', { options });
                    if (options && Array.isArray(options.files) && options.files.length > 0) {
                        const originalFiles = options.files;
                        const originalInsert = options.itemInsertLocation || 'before';
                        let loadedCount = 0;
                        const totalInitialFiles = originalFiles.length;
                        let pondInstance = null;
                        
                        console.log('Antigravity FilePond sorter: sorting ' + totalInitialFiles + ' initial files');
                        
                        const originalOnLoadFile = options.onloadfile;
                        options.onloadfile = function(file) {
                            loadedCount++;
                            console.log('Antigravity FilePond sorter: onloadfile triggered', file.filename, loadedCount + '/' + totalInitialFiles);
                            
                            if (originalOnLoadFile) {
                                originalOnLoadFile.apply(this, arguments);
                            }
                            
                            if (loadedCount >= totalInitialFiles) {
                                setTimeout(() => {
                                    if (pondInstance) {
                                        console.log('Antigravity FilePond sorter: all initial files loaded. Restoring insert location to allow reordering.');
                                        pondInstance.setOptions({
                                            itemInsertLocation: 'after'
                                        });
                                    }
                                }, 150);
                            }
                        };
                        
                        options.itemInsertLocation = (a, b) => {
                            const filenameA = getItemFilename(a);
                            const filenameB = getItemFilename(b);
                            
                            if (!filenameA || !filenameB) {
                                return 0;
                            }
                            
                            const indexA = originalFiles.findIndex(f => getOriginalFilename(f) === filenameA);
                            const indexB = originalFiles.findIndex(f => getOriginalFilename(f) === filenameB);
                            
                            if (indexA !== -1 && indexB !== -1) {
                                return indexB - indexA;
                            }
                            
                            if (indexA !== -1) {
                                return originalInsert === 'before' ? -1 : 1;
                            }
                            
                            if (indexB !== -1) {
                                return originalInsert === 'before' ? 1 : -1;
                            }
                            
                            return 0;
                        };
                        
                        pondInstance = originalCreate(element, options);
                        return pondInstance;
                    }
                    return originalCreate(element, options);
                };
            } else if (attempts > 200) {
                console.warn('Antigravity FilePond sorter: FilePond not found after 200 attempts');
                clearInterval(checkInterval);
            }
        }, 50);
    });
</script>
