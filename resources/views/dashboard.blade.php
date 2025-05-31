@extends('layouts.app')

@section('content')
{{-- <div class="header-actions d-flex justify-content-end">
    <div>
        <a href="{{ route('profile') }}" class="btn btn-outline-success me-2">@lang('Mon profil')</a>
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger">@lang('Se déconnecter')</button>
        </form>
    </div>
</div> --}}

<div class="py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-3">@lang("Welcome")</h2>
    </div>

    <div class="row g-4 mb-4">
        {{-- Section 1: Clients, Suppliers, Products --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center p-3">
                    <h5 class="card-title mb-3">@lang('Clients, Fournisseurs & Produits')</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="/customers" class="btn btn-sm btn-primary w-100">@lang("List of Customers")</a>
                        <a href="/suppliers" class="btn btn-sm btn-primary w-100">@lang("List of Suppliers")</a>
                        <a href="/products" class="btn btn-sm btn-primary w-100">@lang("List of Products")</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Products by Category, Supplier, Store --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center p-3">
                    <h5 class="card-title mb-3">@lang('Produits par Catégorie, Fournisseur, Magasin')</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="/products-by-category" class="btn btn-sm btn-primary w-100">@lang('Produits par catégorie')</a>
                        <a href="/products-by-supplier" class="btn btn-sm btn-primary w-100">@lang('Produits par fournisseur')</a>
                        <a href="/products-by-store" class="btn btn-sm btn-primary w-100">@lang('Produits par magasin')</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Commandes, Mail, Cookies --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center p-3">
                    <h5 class="card-title mb-3">@lang('Commandes & Autres')</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary w-100">@lang('Commandes')</a>
                        <a href="{{ route('mail.form') }}" class="btn btn-sm btn-primary w-100">@lang("Envoyer un email")</a>
                        <a href="/cooksess" class="btn btn-sm btn-primary w-100">@lang("Cookies - Sessions")</a>
                        <a href="{{ route('chart') }}" class="btn btn-sm btn-primary w-100">@lang("Chart")</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <a href="{{ route('ordered.products') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Voir les produits commandés')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('same.products.customers') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Clients avec mêmes commandes')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('products.orders_count') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Commandes par produit')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('products.more_than_6_orders') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Produits populaires')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('orders.totals') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Totaux des commandes')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('orders.greater_than_60') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Commandes de grande valeur')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dashboard.customer_orders') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Commandes clients')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dashboard.suppliers_by_customer') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Fournisseurs par client')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dashboard.products_same_warehouse') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Produits dans mêmes entrepôts')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dashboard.products_per_warehouse') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Products per warehouse')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dashboard.warehouse_values') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Warehouse Values')
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dashboard.warehouses.greater.value') }}" class="btn btn-sm btn-outline-primary w-100">
                        @lang('Warehouses with Value Greater Than') Lind-Gislason
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
