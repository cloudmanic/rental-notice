<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Sleep;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScrapePropertyManagers extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {


        $this->browse(function (Browser $browser) {
            $browser->visit('https://orea.elicense.micropact.com/Lookup/LicenseLookup.aspx')->assertSee('Oregon');

            Sleep::for(60)->seconds();
        });
    }
}
