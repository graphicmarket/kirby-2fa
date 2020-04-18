<template>
  <form class="k-login-form" @submit.prevent="login">
    <h1 class="k-offscreen">{{ $t('login') }}</h1>

    <div v-if="issue" class="k-login-alert" @click="issue = null">
      <span>{{ issue }}</span>
      <k-icon type="alert" />
    </div>

    <template v-if="require2fa">
      <k-text-field v-model="code" v-bind="codeField"></k-text-field>
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
            :value="user.long"
            :label="$t('login.remember')"
            @input="user.long = $event"
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
      code: "",
      isLoading: false,
      issue: "",
      require2fa: false,
      user: {
        email: "",
        password: "",
        long: false
      },
      tfa_session_id: ""
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
    },
    codeField() {
      return {
        name: "code",
        label: "Authentication code",
        minLength: 6,
        maxLength: 6,
        counter: false,
      };
    }
  },
  methods: {
    login() {
      this.issue = null;
      this.isLoading = true;

      if (this.require2fa) {
        this.auth2FA();
      } else {
        this.auth();
      }
    },

    auth() {
      this.$api
        .post("kirby-2fa/auth/login", this.user)
        .then(({ code, user, tfa_session_id }) => {
          if (code === 202) {
            this.require2fa = true;
            this.tfa_session_id = tfa_session_id;
          } else if (code === 200 && user) {
            this.initialize(user);
          }
        })
        .catch(({ message }) => {
          this.issue = message;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    auth2FA() {
      this.$api
        .post("kirby-2fa/auth/code", {
          tfa_session_id: this.tfa_session_id,
          long: this.user.long,
          code: this.code
        })
        .then(({ code, status, user }) => {
          this.initialize(user);
        })
        .catch(err => {
          this.issue = err.message;
          this.isLoading = false;
        });
    },

    initialize(user) {
      this.$store.dispatch("user/current", user);
      this.$store.dispatch("translation/activate", user.language, {
        root: true
      });
      this.$router.push(this.$store.state.path || "/");
      this.$store.dispatch("system/load", true).then(() => {
        this.$store.dispatch("notification/success", this.$t("welcome"));
        this.isLoading = false;
      });
    }
  }
};
</script>

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