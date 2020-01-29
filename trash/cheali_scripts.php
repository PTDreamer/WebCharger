<script>
var voltsPerCell  =
[
//          [ VNominal,           VCharged,           VDischarged,        VStorage,           VvalidEmpty[;
/*None*/    [ 1,                  1,                  1,                  1,                  1},
/*NiCd*/    [ ANALOG_VOLT(1.200), ANALOG_VOLT(1.800), ANALOG_VOLT(0.850), 0,                  ANALOG_VOLT(0.850)],
//http://en.wikipedia.org/wiki/Nickel%E2%80%93metal_hydride_battery
//http://eu.industrial.panasonic.com/sites/default/pidseu/files/downloads/files/ni-mh-handbook-2014_interactive.pdf
//http://www6.zetatalk.com/docs/Batteries/Chemistry/Duracell_Ni-MH_Rechargeable_Batteries_2007.pdf
/*NiMH*/    [ ANALOG_VOLT(1.200), ANALOG_VOLT(1.800), ANALOG_VOLT(1.000), 0,                  ANALOG_VOLT(1.000)],

//Pb based on:
//http://www.battery-usa.com/Catalog/NPAppManual%28Rev0500%29.pdf
//charge start current 0.25C (stage 1 - constant current)
//charge end current 0.05C (end current = start current / 5) (stage 2 - constant voltage)
//Stage 3 (float charge) - not implemented
//http://batteryuniversity.com/learn/article/charging_the_lead_acid_battery
/*Pb*/      [ ANALOG_VOLT(2.000), ANALOG_VOLT(2.450), ANALOG_VOLT(1.750), ANALOG_VOLT(0.000), ANALOG_VOLT(1.900)],

//LiXX
//based on imaxB6 manual
//https://github.com/stawel/cheali-charger/issues/184
/*Life*/    [ ANALOG_VOLT(3.300), ANALOG_VOLT(3.600), ANALOG_VOLT(2.500), ANALOG_VOLT(3.300), ANALOG_VOLT(3.000)],
//based on imaxB6 manual
/*Lilo*/    [ ANALOG_VOLT(3.600), ANALOG_VOLT(4.100), ANALOG_VOLT(2.500), ANALOG_VOLT(3.750), ANALOG_VOLT(3.500)],
/*LiPo*/    [ ANALOG_VOLT(3.700), ANALOG_VOLT(4.200), ANALOG_VOLT(3.000), ANALOG_VOLT(3.850), ANALOG_VOLT(3.209)],
/*Li430*/   [ ANALOG_VOLT(3.700), ANALOG_VOLT(4.300), ANALOG_VOLT(3.000), ANALOG_VOLT(3.850), ANALOG_VOLT(3.209)],
/*Li435*/   [ ANALOG_VOLT(3.700), ANALOG_VOLT(4.350), ANALOG_VOLT(3.000), ANALOG_VOLT(3.850), ANALOG_VOLT(3.209)],

//based on "mars" settings, TODO: find datasheet
/*NiZn*/    [ ANALOG_VOLT(1.600), ANALOG_VOLT(1.900), ANALOG_VOLT(1.300), ANALOG_VOLT(1.600), ANALOG_VOLT(1.400)],

/*Unknown*/ [ 1,                  ANALOG_VOLT(4.000), ANALOG_VOLT(2.000), 1,                  1],
//PowerSupply
/*LED*/     [ 1,                  ANALOG_VOLT(4.000), 1,                  1,                  1],

};
function ANALOG_VOLT(x) {
	return x * 1000;
}
</script>