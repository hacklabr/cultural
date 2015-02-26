//////////////////////////////////////////////////////////////////////////////////////////////////////
// isMobile - Simple way to detect if the user is using a mobile browser
//////////////////////////////////////////////////////////////////////////////////////////////////////

var isMobile = new function () {
    this.Android = function() {
        return navigator.userAgent.match(/Android/i);
    };
    this.BlackBerry = function() {
        return navigator.userAgent.match(/BlackBerry/i);
    };
    // iOS: function() {
    //     return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    // },
    this.iOS = function() {
        return navigator.userAgent.match(/iPhone|iPod/i);
    };
    this.Opera = function() {
        return navigator.userAgent.match(/Opera Mini/i);
    };
    this.Windows = function() {
        return navigator.userAgent.match(/IEMobile/i);
    };
    this.any = function() {
        return (this.Android() || this.BlackBerry() || this.iOS() || this.Opera() || this.Windows());
    };
    this.valueOf = this.toString = function(){
        return this.any();
    };
};