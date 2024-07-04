import axios from "axios";
import { route } from "ziggy-js";

export const getDocuments = async (id: number) => {
    return await axios
        .get(route("documentGroup.show", id))
        .then((response) => {
            if (response.status === 200) {
                return response.data;
            }
        })
        .catch((error) => {
            //console.log(error);
            return error;
        });
};
