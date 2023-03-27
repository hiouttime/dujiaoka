<style>
    .dashboard-title .links {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .dashboard-title .links > a {
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
        color: #fff;
    }
    .dashboard-title h1 {
        font-weight: 200;
        font-size: 2.5rem;
    }
    .dashboard-title .avatar {
        border: none;
        width: 70px;
        height: 70px;
    }
</style>
<script>
    Dcat.ready(function () {
        colorSwitch = window.matchMedia('(prefers-color-scheme: dark)');
        colorSwitch.addListener((e) => { 
            Dcat.darkMode.display(e.matches);
        });
        Dcat.darkMode.display(colorSwitch.matches);
    });
</script>

<div class="dashboard-title card bg-primary">
    <div class="card-body">
        <div class="text-center ">
            <img class="avatar mt-1" src="/assets/common/images/logo.png">

            <div class="text-center mb-1">
                <h1 class="mb-3 mt-2 text-white">独角数卡 V{{ config('dujiaoka.dujiaoka_version', '2.0.0') }}</h1>
                <div class="links">
                    <a href="https://github.com/outtimes/dujiaoka" target="_blank">Github</a>
                    <a href="http://t.me/dujiaoka" id="telegram-group-link" target="_blank">{{ __('dujiaoka.join_telegram_group') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
