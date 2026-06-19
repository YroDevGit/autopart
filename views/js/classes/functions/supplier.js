import { Tyrax } from "../../../code/src/tyrux/main.js";
import { tbl_supplier } from "../db/tables.js";

export async function getSupplier() {
    return await Tyrax.ctrsync({
        action: "select",
        table: tbl_supplier(),
        where: { active: 1 },
        dataOnly: true
    });
}

export async function getSupplierValueLabel() {
    let suppl = await getSupplier();
    let spl = [];

    for (let supp in suppl) {
        let sname = suppl[supp].name ?? "-";
        let address =  suppl[supp].address ?? "-";
        spl[supp] = { value: suppl[supp].id, label:  sname +" - "+ address};
    }
    return spl;
}