<template>
    <section class="bg-white">
        <div class="relative mx-auto px-12 py-8">
            <div class="relative">
                <div>
                    <div class="mx-auto text-left font-medium text-gray-900">
                        <p
                            class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                        >
                            Basic Concepts
                        </p>
                        <p class="mt-3">
                            In nmrXiv, data is organised as
                            <b class="text-teal-700">projects</b>. Consider a
                            project is equivalent to your publication with the
                            corresponding NMR data to be uploaded to nmrXiv. A
                            project can have multiple samples.
                            <b class="text-teal-700"
                                >A sample study corresponds to a group of NMR
                                experiments of one sample</b
                            >, e.g. 1H, 13C, APT, COSY HSQC, HMBC, NOESY in
                            Bruker Format or (another study) in another format
                            such as JCAMP-DX / Varian.
                            <b class="text-teal-700"
                                >Each NMR measurement in a sample study is
                                referred to as a dataset</b
                            >, e.g. 1H NMR or COSY (are each a dataset). You can
                            also publish samples on their own with out creating
                            projects.
                        </p>
                        <div class="w-full">
                            <img
                                src="/img/primer.png"
                                class="h-[calc(40vh)] mx-auto"
                            />
                        </div>
                        <p class="text-md">
                            nmrXiv allows you to upload NMR raw data from any
                            NMR instrument. We can currently auto-detect
                            Bruker/Varian/JOEL formats & JCAMP files and will
                            support more raw & processed file formats soon. Once
                            you upload your raw or processed NMR data, nmrXiv
                            will auto-generate the samples and datasets for you
                            based on the uploaded folder structure.
                            <i
                                ><a
                                    class="text-md"
                                    target="_blank"
                                    href="https://docs.nmrxiv.org/docs/submission-guides/submission/folder-structure"
                                    >More info here</a
                                ></i
                            >
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
