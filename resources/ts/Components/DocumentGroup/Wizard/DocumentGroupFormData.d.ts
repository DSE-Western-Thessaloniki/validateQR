type DocumentGroupFormDataStep1 = {
    completed: boolean;
    name: string;
};

type DocumentGroupFormDataStep2 = {
    completed: boolean;
};

type DocumentGroupFormDataStep3 = {
    completed: boolean;
};

type DocumentGroupFormDataStep4 = {
    completed: boolean;
};

type DocumentGroupFormDataStep5 = {
    completed: boolean;
};

export type DocumentGroupFormData = {
    step1: DocumentGroupFormDataStep1;
    step2: DocumentGroupFormDataStep2;
    step3: DocumentGroupFormDataStep3;
    step4: DocumentGroupFormDataStep4;
    step5: DocumentGroupFormDataStep5;
};
