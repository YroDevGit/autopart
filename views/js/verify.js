import Ctr from "../code/src/mods/ctr";
import FormValidator from "../code/src/mods/formValidator";
import { Twal } from "../code/src/mods/twal";
import { Tyrax } from "../code/src/tyrux/main";
import { tbl_customer } from "./classes/db/tables";

Ctr.submit("#signupForm", (data, formData, form)=>{
    let rules = {
        fullname: {required:true, maxChars:100},
        contact: {required: true, maxChars: 13},
        password: {required: true, minChars: 8}
    }
    let res = FormValidator.validate(data,rules, form);
    if(res.failed){
        console.log(res.errors);
     return;   
    }

    formData = {...formData, email: gEmail, username: gEmail};

    Tyrax.insert({
        table: tbl_customer(),
        data: formData,
        res: (send, code, message, data)=>{
            if(code == 200){
                Tyrax.update({
                    table: "verification",
                    where: {code: gCode},
                    update: {active: 0}
                });
                Twal.ok({
                    title: "Congratulations",
                    text: "You have been registered to Autoparts, you can now login and order"
                }, "/");
            }else{
                Twal.err(message);
            }
        }
    })
    
});

// Additional: auto-focus first field (optional)
window.addEventListener('load', function() {
    document.getElementById('fullname').focus();
});