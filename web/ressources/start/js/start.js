function Zend_ProgressBar_Update(data){
    console.log(data);
    document.getElementById('pg-percent').style.width = data.percent + '%';
    document.getElementById('pg-text-3').innerHTML = data.text;
    document.getElementById('pg-text-percent').innerHTML = data.current + '/' + data.max;
}

function Zend_ProgressBar_Finish(){
    document.getElementById('pg-percent').style.width = '100%';
    
    function go() {
        window.location = "/front/start/index/success";          
    }
    window.setTimeout(go, 1000);
}   
     

$(document).ready(function() {
    if(window.location.pathname == "/front/start/index/install"){
        var iFrame = document.createElement('iframe');
        document.getElementsByTagName('body')[0].appendChild(iFrame);
        iFrame.src = '/front/start/index/install/ready/true';
    }
});