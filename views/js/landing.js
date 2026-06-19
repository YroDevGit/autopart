import Ctr from "../code/src/mods/ctr.js";
import { Twal } from "../code/src/mods/twal.js";
import { Tyrax } from "../code/src/tyrux/main.js";
import { tbl_category, tbl_customer } from "./classes/db/tables.js";

Ctr.submit("#landingLoginForm", async(data)=>{
   let result = await Tyrax.ctrsync({
        table: tbl_customer(),
        action: "findOne",
        where: data
    });
    if(result.empty){
        Twal.err("Invalid login");
        return
    }
    let id = result?.data?.id ?? null;
    if(! id){
        Twal.err("Invalid user login");
        return;
    }

    localStorage.setItem("userid", id);

    Twal.ok("Login success", "/order");
});