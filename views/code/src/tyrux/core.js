document.addEventListener("DOMContentLoaded", ()=>{


    function ctrx_core_init(){
        let allA = document.querySelectorAll("a");
        allA.forEach(element => {
            let value = element.getAttribute("href") ?? "#";
            let isBack = element.hasAttribute("ctrx-back");
            if(value == "#") return;
            if(value.startsWith("http")) return;
            if(value.startsWith("?")) return;

            if(isBack){
                element.removeAttribute("href");
                element.style.cursor ="pointer";
                element.addEventListener("click", ()=>{
                    window.history.back();
                });
            }else{
                let hr = value;
                if(! value.startsWith("/")){
                    hr = "/"+value;
                }
                element.style.cursor ="pointer";
                element.setAttribute("href", hr);
            } 
        });

        let allB = document.querySelectorAll("button");
        allB.forEach(element => {
            let value = element.getAttribute("href") ?? "#";
            let isBack = element.hasAttribute("ctrx-back");
            if(value == "#") return;
            if(value.startsWith("http")) return;
            if(value.startsWith("?")) return;

            if(isBack){
                element.removeAttribute("href");
                element.style.cursor ="pointer";
                element.addEventListener("click", ()=>{
                    window.history.back();
                });
            }else{
                let hr = value;
                if(! value.startsWith("/")){
                    hr = "/"+value;
                }
                element.style.cursor ="pointer";
                element.addEventListener("click", ()=>{
                    location.href = hr;
                });
            } 
        });
    }

    //document.querySelector().


    setTimeout(ctrx_core_init, 500);


});