<template>
    <div v-show="hasSingleSingOn" class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">
                    Or continue with
                </span>
            </div>
        </div>

        <div class="flex mt-3">
            <div v-if="hasOrcidClientID" class="flex-1 w-64 px-1">
                <a
                    href="/auth/login/orcid"
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium hover:bg-gray-50"
                >
                    <span class="sr-only">Sign in with ORCID</span>
                    <img
                        alt="ORCID logo"
                        src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
                        width="20"
                        height="20"
                    />
                </a>
            </div>
            <div v-if="hasTwitterClientID" class="flex-1 w-64 px-1">
                <a
                    href="/auth/login/twitter"
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-sky-500 hover:bg-gray-50"
                >
                    <span class="sr-only">Sign in with Twitter</span>
                    <svg
                        class="w-5 h-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        aria-hidden="true"
                    >
                        <svg
                            style="height: 20px"
                            role="img"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <title>Twitter</title>
                            <path
                                d="M23.643 4.937c-.835.37-1.732.62-2.675.733.962-.576 1.7-1.49 2.048-2.578-.9.534-1.897.922-2.958 1.13-.85-.904-2.06-1.47-3.4-1.47-2.572 0-4.658 2.086-4.658 4.66 0 .364.042.718.12 1.06-3.873-.195-7.304-2.05-9.602-4.868-.4.69-.63 1.49-.63 2.342 0 1.616.823 3.043 2.072 3.878-.764-.025-1.482-.234-2.11-.583v.06c0 2.257 1.605 4.14 3.737 4.568-.392.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.2-.005-.402-.014-.602.91-.658 1.7-1.477 2.323-2.41z"
                            />
                        </svg>
                    </svg>
                </a>
            </div>
            <div v-if="hasGithubClientID" class="flex-1 w-32 px-1">
                <a
                    href="/auth/login/github"
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium hover:bg-gray-50"
                >
                    <span class="sr-only">Sign in with GitHub</span>
                    <svg
                        class="w-5 h-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        hasTwitterClientID() {
            return String(this.$page.props.twitter).toLowerCase() == "true";
        },
        hasGithubClientID() {
            return String(this.$page.props.github).toLowerCase() == "true";
        },
        hasOrcidClientID() {
            return String(this.$page.props.orcid).toLowerCase() == "true";
        },
        hasSingleSingOn() {
            if (
                this.hasTwitterClientID ||
                this.hasGithubClientID ||
                this.hasOrcidClientID
            ) {
                return true;
            } else {
                return false;
            }
        },
    },
};
</script>
