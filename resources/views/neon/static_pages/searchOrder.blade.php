@extends('riniba_03.layouts.default') @section('content')
<main class="content-wrapper">
    <!-- Breadcrumb -->
    <nav class="container pt-2 pt-xxl-3 my-3 my-md-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">主页</a></li>
            <li class="breadcrumb-item"><a href="/">购物</a></li>
            <li class="breadcrumb-item active" aria-current="page">订单查询</li>
        </ol>
    </nav>
    <div class="container mb-3" style="max-width: 680px">
        <div class="col-12">
            <div
                class="alert alert-success text-dark-emphasis fs-sm border-0 rounded-4 mb-0"
                role="alert"
            >
                <div class="card-body text-center">
                    注意：最多只能查询近10笔订单。
                </div>
            </div>
        </div>
    </div>
    <div class="container mb-3" style="max-width: 680px">
        <div class="col-12">
            <div class="card card-body">
                <div class="tab-pane active" id="bordered-tabs-preview">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                data-bs-toggle="tab"
                                href="#order_search_by_sn"
                                >{{ __("dujiaoka.order_search_by_sn") }}</a
                            >
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                data-bs-toggle="tab"
                                href="#order_search_by_email"
                                >{{ __("dujiaoka.order_search_by_email") }}</a
                            >
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                data-bs-toggle="tab"
                                href="#order_search_by_browser"
                                >{{ __("dujiaoka.order_search_by_browser") }}</a
                            >
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- 订单号查询 Tab -->
                        <div class="tab-pane active" id="order_search_by_sn">
                            <form
                                class="needs-validation"
                                action="{{ url('search-order-by-sn') }}"
                                method="post"
                            >
                                {{ csrf_field() }}
                                <div class="mb-3">
                                    <label for="validationCustom01"
                                        >订单号</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="order_sn"
                                        required
                                        placeholder="请输入订单编号"
                                    />
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-dark" type="submit">
                                        {{ __("dujiaoka.search_now") }}
                                    </button>
                                    <button
                                        type="reset"
                                        class="btn btn-secondary"
                                    >
                                        重置
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- 邮箱查询 Tab -->
                        <div class="tab-pane" id="order_search_by_email">
                            <form
                                class="needs-validation"
                                action="{{ url('search-order-by-email') }}"
                                method="post"
                            >
                                {{ csrf_field() }}
                                <div class="mb-3">
                                    <label for="validationCustom01">邮箱</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        required
                                        placeholder="请输入邮箱"
                                    />
                                </div>
                                @if(dujiaoka_config_get('is_open_search_pwd',
                                \App\Models\BaseModel::STATUS_CLOSE) ==
                                \App\Models\BaseModel::STATUS_OPEN)
                                <div class="mb-3">
                                    <label for="validationCustom01"
                                        >{{
                                            __("order.fields.search_pwd")
                                        }}:</label
                                    >
                                    <input
                                        type="password"
                                        class="form-control"
                                        name="search_pwd"
                                        required
                                        placeholder="请输入查询密码"
                                    />
                                </div>
                                @endif
                                <div class="mb-3">
                                    <button class="btn btn-dark" type="submit">
                                        {{ __("dujiaoka.search_now") }}
                                    </button>
                                    <button
                                        type="reset"
                                        class="btn btn-secondary"
                                    >
                                        重置
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- 浏览器缓存查询 Tab -->
                        <div class="tab-pane" id="order_search_by_browser">
                            <form
                                class="needs-validation"
                                action="{{ url('search-order-by-browser') }}"
                                method="post"
                            >
                                {{ csrf_field() }}
                                <div class="mb-3">
                                    <button class="btn btn-dark" type="submit">
                                        {{ __("dujiaoka.search_now") }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@stop @section('js') @stop
