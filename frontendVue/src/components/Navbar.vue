<template>

    <nav class="navbar navbar-expand-lg bg-body-tertiary w-100" id="mainNav">
        <div class="container-fluid w-100">
            <a class="navbar-brand"><strong>Zadanie 2</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <Router-link class="nav-link" to="/">Rozvrh</Router-link>
                    </li>
                    <li class="nav-item">
                        <Router-link class="nav-link" to="/about">Práce</Router-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</template>
