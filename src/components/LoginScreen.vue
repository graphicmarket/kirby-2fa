<template>
  <form class="k-login-form" @submit.prevent="login">
    <h1 class="k-offscreen">{{ $t('login') }}</h1>

    <div v-if="issue" class="k-login-alert" @click="issue = null">
      <span>{{ issue }}</span>
      <k-icon type="alert" />
    </div>

    <template v-if="require2fa">
      <k-text-field v-model="code" name="code" label="Authentication code"></k-text-field>
      <div class="k-login-buttons">
        <k-button class="k-login-button" icon="check" type="submit">
          {{ $t("login") }}
          <template v-if="isLoading">…</template>
        </k-button>
      </div>
    </template>
    <template v-else>
      <k-fieldset :novalidate="true" :fields="fields" v-model="user"></k-fieldset>

      <div class="k-login-buttons">
        <span class="k-login-checkbox">
          <k-checkbox-input
            :value="user.remember"
            :label="$t('login.remember')"
            @input="user.remember = $event"
          />
        </span>
        <k-button class="k-login-button" icon="check" type="submit">
          {{ $t("login") }}
          <template v-if="isLoading">…</template>
        </k-button>
      </div>
    </template>
  </form>
</template>

<script>
export default {
  data() {
    return {
      isLoading: false,
      issue: "",
      require2fa: false,
      code: "",
      user: {
        email: "",
        password: "",
        remember: false
      }
    };
  },
  computed: {
    fields() {
      return {
        email: {
          autofocus: true,
          label: this.$t("email"),
          type: "email",
          required: true,
          link: false
        },
        password: {
          label: this.$t("password"),
          type: "password",
          minLength: 8,
          required: true,
          autocomplete: "current-password",
          counter: false
        }
      };
    }
  },
  methods: {
    login() {
      this.issue = null;
      this.isLoading = true;

      if (this.require2fa) {
        this.verify2FACode();
      } else {
        this.$api
          .post("kirby-2fa/auth", this.user)
          .then(({ valid, tfa, issue }) => {
            if (valid) {
              if (tfa) {
                this.require2fa = tfa;
              } else {
                this.auth();
              }
            } else {
              this.issue = issue;
            }
          })
          .catch(err => {})
          .finally(() => {
            this.isLoading = false;
          });
      }
    },

    auth() {
      this.issue = null;
      this.isLoading = true;
      this.$store
        .dispatch("user/login", this.user)
        .then(() => {
          this.$store.dispatch("system/load", true).then(() => {
            this.$store.dispatch("notification/success", this.$t("welcome"));
            this.isLoading = false;
          });
        })
        .catch(() => {
          this.issue = this.$t("error.access.login");
          this.isLoading = false;
        });
    },

    verify2FACode() {
      this.$api
        .post("kirby-2fa/auth/code", {
          code: this.code,
          email: this.user.email
        })
        .then(({ verify }) => {
          if (verify) {
            this.auth();
          } else {
            this.issue = "Invalid code";
            this.isLoading = false;
          }
        })
        .catch(res => {});
    }
  }
};
</script>

</style>
<style>
.k-login-form label abbr {
  visibility: hidden;
}
.k-login-buttons {
  display: flex;
  align-items: center;
  padding: 1.5rem 0;
}

.k-login-button {
  padding: 0.5rem 1rem;
  font-weight: 500;
  transition: opacity 0.3s;
}

.k-login-button[disabled] {
  opacity: 0.25;
}

.k-login-button:first-child:last-child {
  padding: 0.5rem 1rem 0.5rem 0;
  margin-left: auto;
}

.k-login-alert {
  padding: 0.5rem 0.75rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 38px;
  margin-bottom: 2rem;
  background: #c82829;
  color: #fff;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
}
</style>