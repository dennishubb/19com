"use strict";
var link=getBackendHost();
// Class definition

var KTDropzoneDemo = function () {
    // Private functions
    var sitemap = function () {
		
		
		
        // single file upload
        $('#kt_dropzone_1').dropzone({
			method:'PUT',
            url: 'http://19com_front/cn/backend-admin/seo.html', // Set the url for your upload script location
            // url: "https://keenthemes.com/scripts/void.php", 
			//paramName: "sitemap", // The name that will be used to transfer the file
			//paramName: "sitemap", // The name that will be used to transfer the file
			params:"sitemap:123",
            maxFiles: 1,
            maxFilesize: 5, // MB
			//acceptedFiles: ".txt",
            addRemoveLinks: true,
            accept: function(file, done) {console.log(file);
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            }
        });

        // file type validation
        $('#kt_dropzone_3').dropzone({
            url: "https://keenthemes.com/scripts/void.php", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            acceptedFiles: "txt",
            accept: function(file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            }
        });
    }
	
    return {
        // public functions
        init: function() {
            sitemap();
        }
    };
}();

KTUtil.ready(function() {
    KTDropzoneDemo.init();
});
