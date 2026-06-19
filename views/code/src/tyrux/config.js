/**
 * This is tyrax config
 */

import Ctr from "../mods/ctr.js";

//This is tyrax default header
export const headerHandler = {
    Authorization: "Bearer sometoken",
    "Content-Type": "application/json",
}

/**
 * Error Handler
 * This is tyrax error handler, where catches all http errors
 */
export const errorHandler = (error, message) => {
    /**
     * Default is alert(), you can change it.
     */
    alert(message);
    if(error.code == 707){
        Ctr.redirect("logout");
        return;
    }

}


