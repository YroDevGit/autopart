//Js file for includes/rider/nav
import { getRiderName } from "../../classes/functions/users";

let riderName = await getRiderName(localStorage.getItem("userid"));

Ctr.set_html("#riderName", riderName);