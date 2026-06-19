import Ctr from "../../../code/src/mods/ctr.js";
import TModal from "../../../code/src/mods/tmodal.js";
import Toast from "../../../code/src/mods/toast.js";
import { Twal } from "../../../code/src/mods/twal.js";
import { Tyrax } from "../../../code/src/tyrux/main.js";
import { getCustomerDetails } from "../../classes/functions/customer.js";
import { getUserDetails } from "../../classes/functions/users.js";

let id = localStorage.getItem("userid");

let userDetails = await getUserDetails(id);

let fullname = userDetails.fullname ?? "Unknown"

let profileModal = TModal.init({
    title: "User Profile",
    form_id: "userprofileid",
    form: {
        fullname: { label: "Full name", type: "text", value: fullname },
        password: { label: "New Password", type: "password" },
        rpassword: { label: "Re-Enter password", type: "password" }
    }
})

Ctr.click("#logout_btn", () => {
    Twal.ask("Are you sure to logout?").then((click) => {
        if (click.confirm) {
            Ctr.redirect("logout");
        }
    });
});

Ctr.click("#userprofilelink", () => {
    profileModal.show();
});

profileModal.form_submit((data) => {
    Ctr.set_html(".error_text", '');
    Tyrax.delete({
        url: "user/update",
        data: data,
        res: (send, code, message, data, error) => {
            if (code == 402) {
                Toast.err("Validation failed");
                let errors = send.errors;
                for (let er in errors) {
                    let msg = errors[er];
                    Ctr.errStrSet(er, msg);
                }
            }
            if (code == 401) {
                console.log(message);
                Twal.err(message);
            }
            if (code == 200) {
                Twal.ok("User profile updated", true);
            }
        }
    })
});
