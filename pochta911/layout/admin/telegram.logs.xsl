<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'messages']">
        <div>
            <h2>
                <i class="fa fa-telegram" aria-hidden="true"> </i>
                Журнал Телеграмм
            </h2>
            <hr/>
            <table class="table table-hover table-striped data-table">
                <thead>
                    <th>Дата</th>
                    <th>Отправитель</th>
                    <th>Идентификатор чата</th>
                    <th>Связанный пользователь</th>
                    <th>Сообщение</th>
                    <th>Ответ</th>
                </thead>
                <tbody>
                    <xsl:for-each select="item">
                        <tr>
                            <td>
                                <xsl:value-of select="date"/>
                            </td>
                            <td>
                                <xsl:value-of select="sender"/>
                            </td>
                            <td>
                                <xsl:value-of select="chat_id"/>
                            </td>
                            <td>
                                <xsl:call-template name="users">
                                    <xsl:with-param name="chat_id">
                                        <xsl:value-of select="chat_id"/>
                                    </xsl:with-param>
                                </xsl:call-template>
                            </td>
                            <td>
                                <xsl:value-of select="text"/>
                            </td>
                            <td>
                                <xsl:value-of select="data"/>
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </div>
    </xsl:template>
    <xsl:template name="users">
        <xsl:param name="chat_id"/>
        <xsl:value-of select="/page/body/module/container/users/user[phone_mess=$chat_id]/name"/>
    </xsl:template>
</xsl:stylesheet>
