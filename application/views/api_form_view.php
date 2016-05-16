<style>
.formBox {border:1px solid #ccc; border-radius: 10px; margin: 10px; background:#EFE;}
.formBox div {padding: 1px;}
.formBox h1
{
    color            : #444;
    background-color : #8FA;
    border-bottom    : 1px solid #D0D0D0;
    border-radius    : 10px 10px 0px 0px;
    font-size        : 17px;
    font-weight      : bold;
    margin           : 0 0 14px 0;
    padding          : 14px 15px 10px 15px;
}
#fmURL   {width:700px;}
#fmData  {width:758px; height:150px;}
</style>
<div class='formBox'>
    <h1>API TOOL</h1>
    <div class="body">
        <form method='GET' style='margin:0;'>
            <div>API URL: <input id='fmURL' name='fmURL' type='text' value='{fmURL}'></div>
            <div>環境:
                <select id='fmSite' name='fmSite'>
                    <?php
                        foreach ($this->api_caller_model->site as $site => $site_header)
                        {
                            $selected = ($fmSite == $site) ? 'selected' : '';
                            echo "<option value='{$site}' {$selected}>{$site}</option>";
                        }
                    ?>
                </select>
                <select name='fmIsNewEnv'>
                    <option value='1'>new_application</otpion>
                    <option value='0' <?php echo (!$fmIsNewEnv) ? 'selected' : '' ?>>application</otpion>
                </select>
            </div>
            <div>
                參數:<br>
                <textarea id='fmData' name='fmData'>{fmData}</textarea>
            </div>
            <div>
                <input type='submit' value='送出'>
            </div>
        </form>
    </div>
</div>