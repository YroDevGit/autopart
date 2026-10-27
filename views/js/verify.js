import Ctr from "../code/src/mods/ctr";
import FormValidator from "../code/src/mods/formValidator";
import Loading from "../code/src/mods/loading";
import { Twal } from "../code/src/mods/twal";
import { Tyrax } from "../code/src/tyrux/main";
import { tbl_customer } from "./_classes/db/tables";

Ctr.submit("#signupForm", async (data, formData, form) => {
    let newData = new FormData(document.querySelector("#signupForm"));
    let Vid = newData.get("id");
    let rules = {
        fullname: { required: true, maxChars: 100 },
        contact: { required: true, maxChars: 11, minChars: 11, startsWith: "09" },
        password: { required: true, minChars: 8 }
    }

    if (!Vid.name) {
        Twal.err("Valid ID is required");
        return;
    }
    
    const file = document.querySelector('#idValid').files[0];

    try {
        Loading.load(true);
        let { data: { text } } = await Tesseract.recognize(
            file,
            'eng',
            {}
        );
        text = text.toUpperCase();
        
        let fullname = formData.fullname ?? null;
        fullname = fullname.trim();
        if(! fullname.includes(" ")){
            Twal.err("Please enter a valid Fullname");
            return;
        }

        if(! text.includes("REPUBLIKA NG PILIPINAS") || ! text.includes("PHILIPPINE NATIONAL ID")){
            //Twal.err("Invalid ID");
            //return;
        }

        let splt = fullname.split(" ");
        let isValid = true;
        splt.forEach(element => {
            if (! text.includes(element.toUpperCase()) && ! text.includes(element.toLowerCase())) {
                isValid = false;
            }
        });

        if(isValid == false){
            //Twal.err("There is an error verifying your ID, make sure your credentials matched to your ID details.!");
            //return;
        }
        
    } catch (err) {
        //console.log(err);
        //Twal.err("Can't read your ID, please re-upload a clear image of your ID");
        //return;
    } finally {
        Loading.load(false);
    }

    let res = FormValidator.validate(data, rules, form);
    if (res.failed) {
        console.log(res.errors);
        return;
    }

    newData.delete("id");
    formData = { ...formData, email: gEmail, username: gEmail };
    delete formData.id;

    Tyrax.insert({
        table: tbl_customer(),
        data: formData,
        res: (send, code, message, data) => {
            if (code == 200) {
                Tyrax.update({
                    table: "verification",
                    where: { code: gCode },
                    update: { active: 0 }
                });
                Twal.ok({
                    title: "Congratulations",
                    text: "You have been registered to Autoparts, you can now login and order"
                }, "/");
            } else {
                Twal.err(message);
            }
        }
    })

});

// Additional: auto-focus first field (optional)
window.addEventListener('load', function () {
    document.getElementById('fullname').focus();
});