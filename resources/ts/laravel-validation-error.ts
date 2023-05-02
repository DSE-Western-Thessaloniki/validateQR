import type { AxiosError, AxiosResponse } from "axios";
import axios from "axios";

interface LaravelValidationResponse extends AxiosResponse {
    status: 422;
    data: {
        message: string;
        errors: Record<string, Array<string>>;
    };
}

export interface LaravelValidationError extends AxiosError {
    response: LaravelValidationResponse;
}

function axiosResponseIsLaravelValidationResponse(
    response: AxiosResponse
): response is LaravelValidationResponse {
    return (
        response.status === 422 &&
        typeof response.data?.message === "string" &&
        typeof response.data?.errors === "object"
    );
}

export function isLaravelValidationError(
    error: unknown
): error is LaravelValidationError {
    return Boolean(
        axios.isAxiosError(error) &&
            error.response &&
            axiosResponseIsLaravelValidationResponse(error.response)
    );
}
