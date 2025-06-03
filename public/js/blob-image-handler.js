/**
 * BLOB Image Handler Utility
 * Provides functions for managing binary image data from Laravel APIs
 */
const BlobImageHandler = {
    /**
     * Load an image from a BLOB data URL with cache busting
     * 
     * @param {string} url - The URL to fetch the image from
     * @param {string} imgElementId - ID of the image element to update
     * @param {string} fallbackUrl - Optional fallback URL if image fails to load
     * @param {Function} callback - Optional callback function when image is loaded
     */
    loadImage: function(url, imgElementId, fallbackUrl = null, callback = null) {
        const img = document.getElementById(imgElementId);
        if (!img) return;
        
        // Add cache busting parameter
        const cacheBuster = new Date().getTime();
        const separator = url.includes('?') ? '&' : '?';
        const urlWithCacheBuster = `${url}${separator}v=${cacheBuster}`;
        
        // Set loading state
        if (img.dataset.originalSrc === undefined) {
            img.dataset.originalSrc = img.src;
        }
        img.style.opacity = '0.5';
        
        // Create a new image object to test loading
        const tempImg = new Image();
        tempImg.onload = function() {
            img.src = urlWithCacheBuster;
            img.style.opacity = '1';
            if (callback) callback(true, img);
        };
        
        tempImg.onerror = function() {
            console.error(`Failed to load image from ${url}`);
            if (fallbackUrl) {
                img.src = fallbackUrl;
            } else if (img.dataset.originalSrc) {
                img.src = img.dataset.originalSrc;
            }
            img.style.opacity = '1';
            if (callback) callback(false, img);
        };
        
        tempImg.src = urlWithCacheBuster;
    },
    
    /**
     * Convert a file input to a base64 string
     * 
     * @param {HTMLInputElement} fileInput - The file input element
     * @param {Function} callback - Callback function(base64String, file)
     */
    fileToBase64: function(fileInput, callback) {
        if (!fileInput.files || !fileInput.files[0]) {
            callback(null, null);
            return;
        }
        
        const file = fileInput.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            callback(e.target.result, file);
        };
        
        reader.onerror = function(e) {
            console.error('Error reading file:', e);
            callback(null, file);
        };
        
        reader.readAsDataURL(file);
    },
    
    /**
     * Get metadata for images associated with a document
     * 
     * @param {number} documentId - Document ID
     * @param {Function} callback - Callback function(success, metadata)
     */
    getImageMetadata: function(documentId, callback) {
        fetch(`/api/documentrequest/${documentId}/images/metadata`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                callback(true, data);
            })
            .catch(error => {
                console.error('Error fetching image metadata:', error);
                callback(false, error);
            });
    },
    
    /**
     * Load all document images at once
     * 
     * @param {number} documentId - Document ID
     * @param {Object} imageConfig - Configuration object mapping image types to element IDs
     *                               e.g. {id_front: 'frontImageElement', id_back: 'backImageElement'}
     * @param {Function} callback - Optional callback when all images are loaded
     */
    loadAllDocumentImages: function(documentId, imageConfig, callback = null) {
        this.getImageMetadata(documentId, (success, metadata) => {
            if (!success) {
                if (callback) callback(false, metadata);
                return;
            }
            
            const images = metadata.images;
            const loadedImages = {};
            let loadedCount = 0;
            let totalToLoad = 0;
            
            // Count how many images we'll try to load
            for (const type in imageConfig) {
                if (images[type] && images[type].exists) {
                    totalToLoad++;
                }
            }
            
            if (totalToLoad === 0) {
                if (callback) callback(true, loadedImages);
                return;
            }
            
            // Load each image
            for (const type in imageConfig) {
                if (images[type] && images[type].exists) {
                    const elementId = imageConfig[type];
                    this.loadImage(images[type].url, elementId, null, (success, img) => {
                        loadedCount++;
                        loadedImages[type] = {
                            success: success,
                            element: img
                        };
                        
                        // Call callback when all images are loaded
                        if (loadedCount === totalToLoad && callback) {
                            callback(true, loadedImages);
                        }
                    });
                }
            }
        });
    }
};
