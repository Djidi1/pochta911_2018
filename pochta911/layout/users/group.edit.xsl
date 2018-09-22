<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'groupedit']">
        <div id="cpanel">
            <table class="adminform" align="center">
                <tbody>
                    <tr>
                        <td valign="top">
                            <h2>Редактирование группы:</h2>
                            <form action="http://{//page/@host}/admin/groupUpdate-{group/@group_id}/" method="post">
                                <input type="hidden" value="{group/@group_id}" name="group_id"/>
                                <table align="center">
                                    <tbody>
                                        <tr>
                                            <td>Название:</td>
                                            <td>
                                                <input class="form-control" type="text" name="name" value="{group/@group_name}"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </xsl:template>
</xsl:stylesheet>
