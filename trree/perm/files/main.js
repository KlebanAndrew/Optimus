// динамическая загрузка css
function load_css(css){
    var st = document.createElement("link");
    st.setAttribute("rel","stylesheet");
    st.setAttribute("href", css);
    st.setAttribute("type", "text/css");
    document.getElementsByTagName("head")[0].appendChild(st); 
}

$(document).ready(function() {
    $(".browseTable th div").css("height", ($(".browseTable th").height()-5)+"px");
});