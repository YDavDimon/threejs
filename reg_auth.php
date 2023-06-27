<?php
                            if ($_SESSION['user']['full_name']) {
                                echo('<a href="./profile.php" title="Профиль">
                                <div class="btn" style = " 
                                display: inline-block;
                                padding: 10px 20px;
                                background-color: #dadada;
                                color: #2196F3;
                                border-radius: 5px;
                                margin-right: 10px;
                              " > Профиль </div>
                                </a>');
                            }
                            else echo('<a href="./auth.php" title="войти">
                                <div class="btn" style = " 
                                    display: inline-block;
                                    padding: 10px 20px;
                                    background-color: #dadada;
                                    color: #2196F3;
                                    border-radius: 5px;
                                    margin-right: 10px;
                                  "> Войти </div>
                                </a>  
                        
                                <a href="./reg.php" title="зарегистрироваться">
                                <div class="btn" style = " 
                                display: inline-block;
                                padding: 10px 20px;
                                background-color: #dadada;
                                color: #2196F3;
                                border-radius: 5px;
                                margin-right: 10px;
                              "> Регистрация </div>
                                </a>');
                        ?>